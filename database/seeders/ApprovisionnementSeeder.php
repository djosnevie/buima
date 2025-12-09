<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovisionnementSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;
        $fournisseurId = DB::table('fournisseurs')->first()->id;
        // Use 'admin' or 'user' (Employee) instead of 'manager'
        $userId = DB::table('users')->where('role', '!=', 'super_admin')->first()->id;
        $ingredientId = DB::table('ingredients')->first()->id;

        $approId = DB::table('approvisionnements')->insertGetId([
            'etablissement_id' => $etablissementId,
            'fournisseur_id' => $fournisseurId,
            'numero_bon_livraison' => 'BL-12345',
            'date_approvisionnement' => now()->subDays(5),
            'date_reception' => now()->subDays(5),
            'statut' => 'recu',
            'montant_total' => 150.00,
            'user_id' => $userId,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        DB::table('approvisionnement_items')->insert([
            'approvisionnement_id' => $approId,
            'ingredient_id' => $ingredientId,
            'quantite_commandee' => 10,
            'quantite_recue' => 10,
            'prix_unitaire' => 15.00,
            'sous_total' => 150.00,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);
    }
}