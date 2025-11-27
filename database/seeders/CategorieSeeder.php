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
            ['nom' => 'Entrées', 'type' => 'entree', 'ordre' => 1, 'couleur' => '#10B981'],
            ['nom' => 'Plats Principaux', 'type' => 'plat', 'ordre' => 2, 'couleur' => '#3B82F6'],
            ['nom' => 'Pizzas', 'type' => 'plat', 'ordre' => 3, 'couleur' => '#F59E0B'],
            ['nom' => 'Desserts', 'type' => 'dessert', 'ordre' => 4, 'couleur' => '#EC4899'],
            ['nom' => 'Boissons Soft', 'type' => 'boisson', 'ordre' => 5, 'couleur' => '#6366F1'],
            ['nom' => 'Vins & Bières', 'type' => 'boisson', 'ordre' => 6, 'couleur' => '#8B5CF6'],
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