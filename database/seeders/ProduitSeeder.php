<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;
        $categories = DB::table('categories')->pluck('id', 'nom');

        $produits = [
            ['nom' => 'Salade César', 'categorie' => 'Entrées', 'prix' => 12.00, 'type' => 'entree'],
            ['nom' => 'Soupe à l\'oignon', 'categorie' => 'Entrées', 'prix' => 9.50, 'type' => 'entree'],
            ['nom' => 'Entrecôte Frites', 'categorie' => 'Plats Principaux', 'prix' => 24.00, 'type' => 'plat'],
            ['nom' => 'Burger Maison', 'categorie' => 'Plats Principaux', 'prix' => 18.00, 'type' => 'plat'],
            ['nom' => 'Pizza Margherita', 'categorie' => 'Pizzas', 'prix' => 14.00, 'type' => 'plat'],
            ['nom' => 'Pizza 4 Fromages', 'categorie' => 'Pizzas', 'prix' => 16.50, 'type' => 'plat'],
            ['nom' => 'Tiramisu', 'categorie' => 'Desserts', 'prix' => 8.00, 'type' => 'dessert'],
            ['nom' => 'Crème Brûlée', 'categorie' => 'Desserts', 'prix' => 7.50, 'type' => 'dessert'],
            ['nom' => 'Coca-Cola', 'categorie' => 'Boissons Soft', 'prix' => 4.00, 'type' => 'boisson'],
            ['nom' => 'Evian 50cl', 'categorie' => 'Boissons Soft', 'prix' => 3.50, 'type' => 'boisson'],
            ['nom' => 'Pinte Blonde', 'categorie' => 'Vins & Bières', 'prix' => 7.00, 'type' => 'boisson'],
            ['nom' => 'Verre de Vin Rouge', 'categorie' => 'Vins & Bières', 'prix' => 6.00, 'type' => 'boisson'],
        ];

        foreach ($produits as $p) {
            $produitId = DB::table('produits')->insertGetId([
                'etablissement_id' => $etablissementId,
                'categorie_id' => $categories[$p['categorie']],
                'nom' => $p['nom'],
                'prix_vente' => $p['prix'],
                'type' => $p['type'],
                'tva' => $p['type'] === 'boisson' ? 20 : 10,
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create stock entry for product
            DB::table('stocks_produits')->insert([
                'produit_id' => $produitId,
                'quantite' => rand(10, 100),
                'seuil_alerte' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}