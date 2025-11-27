<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaisseSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        DB::table('caisses')->insert([
            [
                'etablissement_id' => $etablissementId,
                'nom' => 'Caisse Principale',
                'code' => 'CAISSE-01',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'etablissement_id' => $etablissementId,
                'nom' => 'Caisse Bar',
                'code' => 'CAISSE-02',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}