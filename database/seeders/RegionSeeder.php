<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = require_once 'database/files/regions.php';
        foreach ($regions as $region) {
            Region::query()->create([
                'name_ru' => $region['name_ru'],
            ]);
        }
    }
}
