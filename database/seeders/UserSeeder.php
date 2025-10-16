<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@emercerie.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Mercerie Belle Couture',
            'email' => 'belle@emercerie.com',
            'password' => Hash::make('password'),
            'role' => 'mercerie',
        ]);

        User::create([
            'name' => 'Couturier Alain',
            'email' => 'alain@emercerie.com',
            'password' => Hash::make('password'),
            'role' => 'couturier',
        ]);
    }
}
