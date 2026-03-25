<?php

namespace App\Http\Controllers;

use App\Models\SessionCaisse;
use App\Models\CommandeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseSessionPrintController extends Controller
{
    public function printGlobal($id)
    {
        $session = SessionCaisse::with(['caisse', 'user'])->findOrFail($id);
        
        // Ensure user belongs to the same etablissement
        abort_if($session->user->etablissement_id !== auth()->user()->etablissement_id && !auth()->user()->isAdmin(), 403);

        // Calculate Boisson Sales
        $boissonSales = CommandeItem::join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->where('commandes.session_caisse_id', $session->id)
            ->where('commandes.statut', 'payee')
            ->where('produits.type', 'boisson')
            ->sum('commande_items.sous_total');

        // Calculate Food Sales (anything not boisson)
        $foodSales = CommandeItem::join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->where('commandes.session_caisse_id', $session->id)
            ->where('commandes.statut', 'payee')
            ->where('produits.type', '!=', 'boisson')
            ->sum('commande_items.sous_total');

        // Servers sales for this session
        $serversSales = DB::table('commandes')
            ->join('users', 'commandes.user_id', '=', 'users.id')
            ->where('commandes.session_caisse_id', $session->id)
            ->where('commandes.statut', 'payee')
            ->select('users.name', DB::raw('SUM(commandes.total) as total'))
            ->groupBy('users.name')
            ->get();

        // Calculate total CA
        $caTotal = $boissonSales + $foodSales;

        // Montant Carte
        $montantCarte = DB::table('transactions')
            ->where('session_caisse_id', $session->id)
            ->where('type', 'vente')
            ->where('mode_paiement', 'carte')
            ->sum('montant');

        // Sorties de caisse (Dépense journalière)
        $depenseJournaliere = DB::table('transactions')
            ->where('session_caisse_id', $session->id)
            ->where('type', 'sortie_caisse')
            ->sum('montant');

        return view('reports.print.session-global', compact(
            'session', 'boissonSales', 'foodSales', 'caTotal', 'serversSales', 'montantCarte', 'depenseJournaliere'
        ));
    }

    public function printFood($id)
    {
        $session = SessionCaisse::with(['caisse', 'user'])->findOrFail($id);
        abort_if($session->user->etablissement_id !== auth()->user()->etablissement_id && !auth()->user()->isAdmin(), 403);

        $items = CommandeItem::join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->where('commandes.session_caisse_id', $session->id)
            ->where('commandes.statut', 'payee')
            ->where('produits.type', '!=', 'boisson')
            ->select('produits.nom as designation', 'commande_items.prix_unitaire as pu', DB::raw('SUM(commande_items.quantite) as qte'), DB::raw('SUM(commande_items.sous_total) as ttc'))
            ->groupBy('produits.nom', 'commande_items.prix_unitaire')
            ->get();

        $total = $items->sum('ttc');

        return view('reports.print.session-food', compact('session', 'items', 'total'));
    }

    public function printDrinks($id)
    {
        $session = SessionCaisse::with(['caisse', 'user'])->findOrFail($id);
        abort_if($session->user->etablissement_id !== auth()->user()->etablissement_id && !auth()->user()->isAdmin(), 403);

        $items = CommandeItem::join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
            ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
            ->where('commandes.session_caisse_id', $session->id)
            ->where('commandes.statut', 'payee')
            ->where('produits.type', 'boisson')
            ->select('produits.nom as designation', 'commande_items.prix_unitaire as pu', DB::raw('SUM(commande_items.quantite) as qte'), DB::raw('SUM(commande_items.sous_total) as ttc'))
            ->groupBy('produits.nom', 'commande_items.prix_unitaire')
            ->get();

        $total = $items->sum('ttc');

        return view('reports.print.session-drinks', compact('session', 'items', 'total'));
    }
}
