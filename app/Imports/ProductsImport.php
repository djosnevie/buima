<?php

namespace App\Imports;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\StockProduit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $etablissementId = auth()->user()->etablissement_id;
        
        foreach ($rows as $row) {
            $nom = $row['nom_produit'] ?? null;
            if (!$nom) continue;

            $prix = $row['prix'] ?? 0;
            $qMinimum = $row['q_minimum'] ?? 0;
            $catNom = $row['categorie'] ?? null;
            $categorieId = null;

            if ($catNom) {
                // Find or create category
                $cat = Categorie::firstOrCreate(
                    [
                        'nom' => $catNom, 
                        'etablissement_id' => $etablissementId
                    ],
                    [
                        'type' => 'plat', // Default type
                        'couleur' => '#3B82F6',
                        'ordre' => (Categorie::where('etablissement_id', $etablissementId)->max('ordre') ?? 0) + 1,
                    ]
                );
                $categorieId = $cat->id;
            }

            // Find or create product
            $produit = Produit::updateOrCreate(
                [
                    'nom' => $nom,
                    'etablissement_id' => $etablissementId
                ],
                [
                    'prix_vente' => $prix,
                    'categorie_id' => $categorieId,
                    'gestion_stock' => $qMinimum > 0, // Enable stock if there's a minimum
                    'disponible' => true,
                ]
            );

            // Update or create stock info
            if ($produit->gestion_stock || $qMinimum > 0) {
                StockProduit::updateOrCreate(
                    ['produit_id' => $produit->id],
                    [
                        'seuil_alerte' => $qMinimum,
                        // Do not overwrite existing quantity to 0 if it already exists
                        'quantite' => StockProduit::where('produit_id', $produit->id)->value('quantite') ?? 0
                    ]
                );
            }
        }
    }
}
