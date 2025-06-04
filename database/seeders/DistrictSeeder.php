<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = require_once 'database/files/districts.php';
        foreach ($districts as $district) {
            District::query()->create([
                'region_id' => $district['region_id'],
                'name_ru'   => $district['name_ru'],
            ]);
        }
    }
}
