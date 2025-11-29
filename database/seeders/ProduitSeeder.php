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
            // Entrées
            ['nom' => 'Salade de crudités', 'categorie' => 'Entrée', 'prix' => 10000.00, 'type' => 'entree'],
            ['nom' => 'Salade piémontaise', 'categorie' => 'Entrée', 'prix' => 2500.00, 'type' => 'entree'],
            ['nom' => 'Salade russe', 'categorie' => 'Entrée', 'prix' => 1000.00, 'type' => 'entree'],

            // Plats
            ['nom' => 'Sauté de poisson salé aux aubergines', 'categorie' => 'Plat', 'prix' => 10000.00, 'type' => 'plat'],
            ['nom' => 'Bouillon sauvage', 'categorie' => 'Plat', 'prix' => 10000.00, 'type' => 'plat'],
            ['nom' => 'Bouillon Nzombo', 'categorie' => 'Plat', 'prix' => 5000.00, 'type' => 'plat'],
            ['nom' => 'Mbinzo sauté aux oignons', 'categorie' => 'Plat', 'prix' => 10000.00, 'type' => 'plat'],
            ['nom' => 'Légumes verts', 'categorie' => 'Plat', 'prix' => 10000.00, 'type' => 'plat'],
            ['nom' => 'Saka saka', 'categorie' => 'Plat', 'prix' => 5000.00, 'type' => 'plat'],
            ['nom' => 'Liboke ya nsungi (poisson...', 'categorie' => 'Plat', 'prix' => 2500.00, 'type' => 'plat'],

            // Grillades
            ['nom' => 'Manioc', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Riz gras', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Bananes vapeur', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Tubercules', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Bananes', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Ailes de poulet', 'categorie' => 'Grillade', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Le vrai Poulet mayo (entier)', 'categorie' => 'Grillade', 'prix' => 10000.00, 'type' => 'plat'],
            ['nom' => 'Le vrai Poulet mayo (demi)', 'categorie' => 'Grillade', 'prix' => 5000.00, 'type' => 'plat'],
            ['nom' => 'Cuisse de poulet', 'categorie' => 'Grillade', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Ntaba ya ko kamba', 'categorie' => 'Grillade', 'prix' => 4000.00, 'type' => 'plat'],
            ['nom' => 'Ngala à la kinoise', 'categorie' => 'Grillade', 'prix' => 5000.00, 'type' => 'plat'],
            ['nom' => 'Cuisse de poulet', 'categorie' => 'Grillade', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Ngolo (Silure)', 'categorie' => 'Grillade', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Bastillon', 'categorie' => 'Grillade', 'prix' => 2000.00, 'type' => 'plat'],
            ['nom' => 'Nzombo', 'categorie' => 'Grillade', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Pain', 'categorie' => 'Grillade', 'prix' => 1000.00, 'type' => 'plat'],

            // Accompagnements
            ['nom' => 'Manioc', 'categorie' => 'Accompagnement', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Riz gras', 'categorie' => 'Accompagnement', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Bananes vapeur', 'categorie' => 'Accompagnement', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Tubercules', 'categorie' => 'Accompagnement', 'prix' => 1000.00, 'type' => 'plat'],
            ['nom' => 'Bananes', 'categorie' => 'Accompagnement', 'prix' => 1000.00, 'type' => 'plat'],

            // Boissons
            ['nom' => 'Orangina', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Ngok', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Black', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Heineken', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Booster rouge', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Booster gin tonic', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Guinness', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Jus', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Eau', 'categorie' => 'Boissons', 'prix' => 500.00, 'type' => 'boisson'],
            ['nom' => 'Eau grande bouteille', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Desperados', 'categorie' => 'Boissons', 'prix' => 2000.00, 'type' => 'boisson'],
            ['nom' => 'Bouifort', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => '33 Export', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Primus', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Doppel', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Racine', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Castel', 'categorie' => 'Boissons', 'prix' => 1000.00, 'type' => 'boisson'],

            // Cocktails sans alcool
            ['nom' => 'Bora Bora', 'categorie' => 'Cocktails sans alcool', 'prix' => 4000.00, 'type' => 'boisson'],
            ['nom' => 'Virgin Colada', 'categorie' => 'Cocktails sans alcool', 'prix' => 2500.00, 'type' => 'boisson'],
            ['nom' => 'Spécialité Barmaid', 'categorie' => 'Cocktails sans alcool', 'prix' => 2500.00, 'type' => 'boisson'],
            ['nom' => 'Ciel Blue', 'categorie' => 'Cocktails sans alcool', 'prix' => 2500.00, 'type' => 'boisson'],

            // Cocktails alcoolisés
            ['nom' => 'Margarita', 'categorie' => 'Cocktails alcoolisés', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Sex on the beach', 'categorie' => 'Cocktails alcoolisés', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Pina Colada', 'categorie' => 'Cocktails alcoolisés', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Signature', 'categorie' => 'Cocktails alcoolisés', 'prix' => 1000.00, 'type' => 'boisson'],

            // Maboke
            ['nom' => 'Ngolo (Silure)', 'categorie' => 'Maboke', 'prix' => 2500.00, 'type' => 'plat'],

            // Cocktail (catégorie générale)
            ['nom' => 'Spaghettis Napolitaine', 'categorie' => 'Cocktail', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Spaghettis à la viande', 'categorie' => 'Cocktail', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Spaghettis au poulet effiloc...', 'categorie' => 'Cocktail', 'prix' => 2500.00, 'type' => 'plat'],
            ['nom' => 'Onangtine', 'categorie' => 'Cocktail', 'prix' => 1000.00, 'type' => 'boisson'],
            ['nom' => 'Desperados', 'categorie' => 'Cocktail', 'prix' => 2000.00, 'type' => 'boisson'],
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