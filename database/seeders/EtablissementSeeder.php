<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtablissementSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('etablissements')->insert([
            'nom' => 'Le Gourmet Parisien',
            'type' => 'mixte',
            'adresse' => '12 Avenue des Champs-Élysées, 75008 Paris',
            'telephone' => '01 23 45 67 89',
            'email' => 'contact@legourmet.fr',
            'logo' => 'logo.png',
            'configuration' => json_encode([
                'tva' => 20,
                'monnaie' => 'EUR',
                'horaires' => [
                    'lundi' => ['11:00-15:00', '18:00-23:00'],
                    'mardi' => ['11:00-15:00', '18:00-23:00'],
                    'mercredi' => ['11:00-15:00', '18:00-23:00'],
                    'jeudi' => ['11:00-15:00', '18:00-23:00'],
                    'vendredi' => ['11:00-15:00', '18:00-00:00'],
                    'samedi' => ['11:00-15:00', '18:00-00:00'],
                    'dimanche' => ['11:00-16:00']
                ]
            ]),
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}