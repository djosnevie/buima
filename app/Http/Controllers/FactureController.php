<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    /**
     * Afficher la facture d'une commande
     */
    public function show($id)
    {
        // Récupérer la commande avec toutes ses relations
        $commande = Commande::with([
            'items.produit',
            'table',
            'user'
        ])->findOrFail($id);

        // Informations de l'établissement (à adapter selon votre modèle)
        $etablissement = [
            'nom' => 'Restaurant O\'Menu',
            'adresse' => '123 Rue de la Gastronomie',
            'ville' => 'Paris 75001',
            'telephone' => '+33 1 23 45 67 89',
            'email' => 'contact@omenu.fr',
            'siret' => '123 456 789 00012',
            'tva' => 'FR 12 345678901'
        ];

        return view('pages.orders.invoice', compact('commande', 'etablissement'));
    }
}
