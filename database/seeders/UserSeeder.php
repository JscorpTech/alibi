<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //        User::factory(100)->create();
        User::query()->create([
            'full_name' => 'Admin',
            'phone'     => '998943990509',
            'role'      => RoleEnum::ADMIN,
            'password'  => Hash::make('12345678'),
        ]);
    }
}
