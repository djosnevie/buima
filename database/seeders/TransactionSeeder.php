<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;
        $sessionCaisseId = DB::table('sessions_caisse')->where('statut', 'fermee')->first()->id;
        $userId = DB::table('users')->where('role', 'cashier')->first()->id;
        $factures = DB::table('factures')->get();

        foreach ($factures as $facture) {
            DB::table('transactions')->insert([
                'etablissement_id' => $etablissementId,
                'session_caisse_id' => $sessionCaisseId,
                'facture_id' => $facture->id,
                'type' => 'vente',
                'montant' => $facture->montant_total,
                'mode_paiement' => $facture->mode_paiement,
                'description' => 'Encaissement facture ' . $facture->numero_facture,
                'user_id' => $userId,
                'date_transaction' => $facture->date_paiement,
                'created_at' => $facture->created_at,
                'updated_at' => $facture->updated_at,
            ]);
        }
    }
}