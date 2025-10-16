<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@emercerie.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@emercerie.com',
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'admin',
            ]);
            $this->command->info('Super administrateur créé : admin@emercerie.com / ChangeMe123!');
        } else {
            $this->command->warn('Super administrateur déjà existant, aucun nouvel enregistrement créé.');
        }
    }
}
