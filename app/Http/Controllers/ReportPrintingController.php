<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportPrintingController extends Controller
{
    public function show(Request $request, $type)
    {
        $etablissement = Auth::user()->etablissement;

        $dateRange = $request->query('range', 'today');
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        // Determine dates
        if ($dateRange === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            switch ($dateRange) {
                case 'week':
                    $start = Carbon::now()->startOfWeek();
                    $end = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                    break;
                case 'year':
                    $start = Carbon::now()->startOfYear();
                    $end = Carbon::now()->endOfYear();
                    break;
                case 'today':
                default:
                    $start = Carbon::today()->startOfDay();
                    $end = Carbon::today()->endOfDay();
                    break;
            }
        }

        $data = [
            'type' => $type,
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y'),
            'etablissement' => $etablissement,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $query = Commande::where('etablissement_id', $etablissement->id)
            ->whereBetween('created_at', [$start, $end]);

        switch ($type) {
            case 'sales':
                $data['title'] = 'Rapport des Ventes';
                $data['daily_sales'] = $query->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy(DB::raw('DATE(created_at)'))
                    ->get();
                $data['total_revenue'] = $query->sum('total');
                break;

            case 'products':
                $data['title'] = 'Top Produits';
                $data['products'] = DB::table('commande_items')
                    ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                    ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end])
                    ->select('produits.nom', DB::raw('SUM(commande_items.quantite) as qty'), DB::raw('SUM(commande_items.sous_total) as revenue'))
                    ->groupBy('produits.nom')
                    ->orderByDesc('qty')
                    ->limit(20)
                    ->get();
                break;

            case 'categories':
                $data['title'] = 'Ventes par Catégorie';
                $data['categories'] = DB::table('commande_items')
                    ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                    ->join('produits', 'commande_items.produit_id', '=', 'produits.id')
                    ->join('categories', 'produits.categorie_id', '=', 'categories.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end])
                    ->select('categories.nom', DB::raw('SUM(commande_items.sous_total) as revenue'), DB::raw('COUNT(commande_items.id) as count'))
                    ->groupBy('categories.nom')
                    ->orderByDesc('revenue')
                    ->get();
                break;

            case 'staff':
                $data['title'] = 'Performance Serveurs';
                $data['staff'] = DB::table('commandes')
                    ->join('users', 'commandes.user_id', '=', 'users.id')
                    ->where('commandes.etablissement_id', $etablissement->id)
                    ->whereBetween('commandes.created_at', [$start, $end])
                    ->select('users.name', DB::raw('COUNT(commandes.id) as count'), DB::raw('SUM(commandes.total) as revenue'))
                    ->groupBy('users.name')
                    ->orderByDesc('revenue')
                    ->get();
                break;

            case 'payment':
                $data['title'] = 'Méthodes de Paiement';
                $data['payments'] = $query->select('statut', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
                    ->groupBy('statut')
                    ->get();
                break;

            case 'product_list':
                $data['title'] = 'Liste des Produits';
                $data['start'] = null;
                $data['end'] = null;
                $data['product_list'] = DB::table('produits')
                    ->leftJoin('categories', 'produits.categorie_id', '=', 'categories.id')
                    ->where('produits.etablissement_id', $etablissement->id)
                    ->select('produits.nom', 'produits.prix_vente as prix', 'categories.nom as categorie', 'produits.image')
                    ->orderBy('categories.nom')
                    ->orderBy('produits.nom')
                    ->get();
                break;
        }

        return view('reports.print.generic', $data);
    }
}
