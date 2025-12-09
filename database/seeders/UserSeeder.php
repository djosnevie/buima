<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one restaurant exists
        $etablissementId = DB::table('etablissements')->first()->id ?? DB::table('etablissements')->insertGetId([
            'nom' => 'Restaurant BelOne',
            'type' => 'mixte',
            'devise' => 'XAF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@omenu.com',
                'role' => 'super_admin',
                'password' => Hash::make('password'),
                'etablissement_id' => null,
            ],
            [
                'name' => 'Admin Restaurant',
                'email' => 'admin@omenu.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'etablissement_id' => $etablissementId,
            ],
            [
                'name' => 'Serveur Pierre',
                'email' => 'serveur@omenu.com',
                'role' => 'user',
                'password' => Hash::make('password'),
                'etablissement_id' => $etablissementId,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}