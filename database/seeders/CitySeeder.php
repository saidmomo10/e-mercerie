<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Cotonou' => ['Akpakpa','Haie Vive','Zongo','Gbedjromede'],
            'Porto-Novo' => ['Aflagbeto','Adjohoun','Gbegamey'],
            'Abomey-Calavi' => ['Godomey','Kpanroun','Hêvié'],
            'Parakou' => ['Gare','Cité','Djougou'],
        ];

        foreach ($cities as $name => $quarters) {
            $city = City::firstOrCreate(['name' => $name]);
            foreach ($quarters as $q) {
                $city->quarters()->firstOrCreate(['name' => $q]);
            }
        }
    }
}
