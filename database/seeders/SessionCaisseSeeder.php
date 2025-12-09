<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionCaisseSeeder extends Seeder
{
    public function run(): void
    {
        $caisseId = DB::table('caisses')->first()->id;
        // Use 'admin' or 'user' (Employee) instead of 'cashier' which doesn't exist anymore
        // We pick the first user who is NOT a super admin
        $userId = DB::table('users')->where('role', '!=', 'super_admin')->first()->id;

        // Session fermée hier
        DB::table('sessions_caisse')->insert([
            'caisse_id' => $caisseId,
            'user_id' => $userId,
            'date_ouverture' => now()->subDay()->setHour(10)->setMinute(0),
            'date_fermeture' => now()->subDay()->setHour(23)->setMinute(0),
            'fond_caisse_depart' => 200.00,
            'fond_caisse_theorique' => 1500.00,
            'fond_caisse_reel' => 1500.00,
            'ecart' => 0,
            'statut' => 'fermee',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        // Session ouverte aujourd'hui
        DB::table('sessions_caisse')->insert([
            'caisse_id' => $caisseId,
            'user_id' => $userId,
            'date_ouverture' => now()->setHour(10)->setMinute(0),
            'date_fermeture' => null,
            'fond_caisse_depart' => 200.00,
            'fond_caisse_theorique' => 200.00,
            'fond_caisse_reel' => null,
            'ecart' => 0,
            'statut' => 'ouverte',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}