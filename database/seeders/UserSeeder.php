<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $etablissementId = DB::table('etablissements')->first()->id;

        $users = [
            [
                'name' => 'Admin System',
                'email' => 'admin@omenu.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jean Gérant',
                'email' => 'gerant@omenu.com',
                'role' => 'manager',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sophie Serveuse',
                'email' => 'service@omenu.com',
                'role' => 'server',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Marc Cuisine',
                'email' => 'cuisine@omenu.com',
                'role' => 'kitchen',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Lucie Caisse',
                'email' => 'caisse@omenu.com',
                'role' => 'cashier',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}