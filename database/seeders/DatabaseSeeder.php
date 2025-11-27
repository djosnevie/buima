<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EtablissementSeeder::class,
            UserSeeder::class,
            FournisseurSeeder::class,
            IngredientSeeder::class,
            TableSeeder::class,
            CategorieSeeder::class,
            ProduitSeeder::class,
            CaisseSeeder::class,
            SessionCaisseSeeder::class,
            CommandeSeeder::class,
            FactureSeeder::class,
            TransactionSeeder::class,
            ApprovisionnementSeeder::class,
        ]);
    }
}