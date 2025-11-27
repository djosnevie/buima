<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        $zones = [
            'Salle Principale' => range(1, 10),
            'Terrasse' => range(11, 20),
            'Étage' => range(21, 25),
        ];

        foreach ($zones as $zone => $numeros) {
            foreach ($numeros as $num) {
                DB::table('tables')->insert([
                    'etablissement_id' => $etablissementId,
                    'numero' => (string)$num,
                    'capacite' => rand(2, 6),
                    'zone' => $zone,
                    'statut' => 'libre',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}