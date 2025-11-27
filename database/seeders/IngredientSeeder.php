<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        $ingredients = [
            ['nom' => 'Farine T55', 'unite' => 'kg', 'prix_achat_moyen' => 1.20, 'stock_actuel' => 50],
            ['nom' => 'Tomates', 'unite' => 'kg', 'prix_achat_moyen' => 2.50, 'stock_actuel' => 20],
            ['nom' => 'Mozzarella', 'unite' => 'kg', 'prix_achat_moyen' => 8.00, 'stock_actuel' => 15],
            ['nom' => 'Jambon de Paris', 'unite' => 'kg', 'prix_achat_moyen' => 12.00, 'stock_actuel' => 10],
            ['nom' => 'Oeufs', 'unite' => 'piece', 'prix_achat_moyen' => 0.25, 'stock_actuel' => 200],
            ['nom' => 'Steak Haché', 'unite' => 'kg', 'prix_achat_moyen' => 15.00, 'stock_actuel' => 30],
            ['nom' => 'Pommes de terre', 'unite' => 'kg', 'prix_achat_moyen' => 0.80, 'stock_actuel' => 100],
            ['nom' => 'Coca-Cola 33cl', 'unite' => 'piece', 'prix_achat_moyen' => 0.60, 'stock_actuel' => 240],
            ['nom' => 'Vin Rouge Maison', 'unite' => 'l', 'prix_achat_moyen' => 4.50, 'stock_actuel' => 50],
            ['nom' => 'Café Grain', 'unite' => 'kg', 'prix_achat_moyen' => 18.00, 'stock_actuel' => 10],
        ];

        foreach ($ingredients as $i) {
            DB::table('ingredients')->insert(array_merge($i, [
                'etablissement_id' => $etablissementId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}