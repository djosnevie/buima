<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommandeSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;
        $userId = DB::table('users')->where('role', 'server')->first()->id;
        $tableId = DB::table('tables')->first()->id;
        $produits = DB::table('produits')->limit(3)->get();

        // Commande terminée hier
        $commandeId = DB::table('commandes')->insertGetId([
            'etablissement_id' => $etablissementId,
            'table_id' => $tableId,
            'numero_commande' => 'CMD-' . now()->subDay()->format('Ymd') . '-001',
            'type_commande' => 'sur_place',
            'user_id' => $userId,
            'statut' => 'payee',
            'sous_total' => 50.00,
            'total' => 50.00,
            'date_commande' => now()->subDay()->setHour(12),
            'heure_prise' => now()->subDay()->setHour(12),
            'heure_service' => now()->subDay()->setHour(12)->addMinutes(20),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        foreach ($produits as $p) {
            DB::table('commande_items')->insert([
                'commande_id' => $commandeId,
                'produit_id' => $p->id,
                'quantite' => 1,
                'prix_unitaire' => $p->prix_vente,
                'sous_total' => $p->prix_vente,
                'statut' => 'servi',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]);
        }

        // Commande en cours aujourd'hui
        $commandeId = DB::table('commandes')->insertGetId([
            'etablissement_id' => $etablissementId,
            'table_id' => $tableId,
            'numero_commande' => 'CMD-' . now()->format('Ymd') . '-001',
            'type_commande' => 'sur_place',
            'user_id' => $userId,
            'statut' => 'en_preparation',
            'sous_total' => 30.00,
            'total' => 30.00,
            'date_commande' => now(),
            'heure_prise' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        foreach ($produits as $p) {
             DB::table('commande_items')->insert([
                'commande_id' => $commandeId,
                'produit_id' => $p->id,
                'quantite' => 1,
                'prix_unitaire' => $p->prix_vente,
                'sous_total' => $p->prix_vente,
                'statut' => 'en_preparation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}