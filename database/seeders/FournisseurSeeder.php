<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        $fournisseurs = [
            ['nom' => 'Metro Cash & Carry', 'contact' => 'Service Pro', 'telephone' => '0100000001', 'email' => 'commande@metro.fr'],
            ['nom' => 'Boucherie du Centre', 'contact' => 'M. Leblanc', 'telephone' => '0100000002', 'email' => 'viande@boucherie.fr'],
            ['nom' => 'Primeurs & Co', 'contact' => 'Mme. Vert', 'telephone' => '0100000003', 'email' => 'legumes@primeurs.fr'],
            ['nom' => 'Boissons Service', 'contact' => 'Jean-Paul', 'telephone' => '0100000004', 'email' => 'contact@boissons.fr'],
        ];

        foreach ($fournisseurs as $f) {
            DB::table('fournisseurs')->insert(array_merge($f, [
                'etablissement_id' => $etablissementId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}