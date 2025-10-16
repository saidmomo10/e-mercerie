<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supply;

class SuppliesSeeder extends Seeder
{
    public function run(): void
    {
        $supplies = [
            ['name' => 'Tissu coton 1m', 'category' => 'Tissus', 'unit' => 'm'],
            ['name' => 'Fil polyester 100m', 'category' => 'Fils', 'unit' => 'pcs'],
            ['name' => 'Bouton 15mm', 'category' => 'Accessoires', 'unit' => 'pcs'],
            ['name' => 'Fermeture éclair 20cm', 'category' => 'Accessoires', 'unit' => 'pcs'],
            ['name' => 'Aiguille de couture', 'category' => 'Accessoires', 'unit' => 'pcs'],
            ['name' => 'Élastique 3cm', 'category' => 'Accessoires', 'unit' => 'm'],
            ['name' => 'Biais 20mm', 'category' => 'Accessoires', 'unit' => 'm'],
        ];

        foreach ($supplies as $item) {
            Supply::firstOrCreate(['name' => $item['name']], $item);
        }

        $this->command->info(count($supplies).' fournitures ajoutées.');
    }
}
