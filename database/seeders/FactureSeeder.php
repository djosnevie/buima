<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FactureSeeder extends Seeder
{
    public function run(): void
    {
        // Get paid commands
        $commandes = DB::table('commandes')->where('statut', 'payee')->get();

        foreach ($commandes as $cmd) {
            DB::table('factures')->insert([
                'commande_id' => $cmd->id,
                'numero_facture' => 'FAC-' . now()->subDay()->format('Ymd') . '-' . $cmd->id,
                'montant_total' => $cmd->total,
                'montant_ht' => $cmd->total / 1.1, // Approx
                'montant_tva' => $cmd->total - ($cmd->total / 1.1),
                'montant_paye' => $cmd->total,
                'montant_rendu' => 0,
                'mode_paiement' => 'carte_bancaire',
                'statut' => 'payee',
                'date_emission' => $cmd->updated_at,
                'date_paiement' => $cmd->updated_at,
                'created_at' => $cmd->updated_at,
                'updated_at' => $cmd->updated_at,
            ]);
        }
    }
}