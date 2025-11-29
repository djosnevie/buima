<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        $categories = [
            ['nom' => 'Entrée', 'type' => 'entree', 'ordre' => 1, 'couleur' => '#10B981'],
            ['nom' => 'Plat', 'type' => 'plat', 'ordre' => 2, 'couleur' => '#3B82F6'],
            ['nom' => 'Grillade', 'type' => 'plat', 'ordre' => 3, 'couleur' => '#F59E0B'],
            ['nom' => 'Accompagnement', 'type' => 'plat', 'ordre' => 4, 'couleur' => '#8B5CF6'],
            ['nom' => 'Boissons', 'type' => 'boisson', 'ordre' => 5, 'couleur' => '#6366F1'],
            ['nom' => 'Cocktail', 'type' => 'boisson', 'ordre' => 6, 'couleur' => '#EC4899'],
            ['nom' => 'Maboke', 'type' => 'plat', 'ordre' => 7, 'couleur' => '#14B8A6'],
            ['nom' => 'Cocktails sans alcool', 'type' => 'boisson', 'ordre' => 8, 'couleur' => '#F97316'],
            ['nom' => 'Cocktails alcoolisés', 'type' => 'boisson', 'ordre' => 9, 'couleur' => '#A855F7'],
        ];

        foreach ($categories as $c) {
            DB::table('categories')->insert(array_merge($c, [
                'etablissement_id' => $etablissementId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}