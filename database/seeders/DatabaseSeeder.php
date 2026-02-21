<?php

namespace Database\Seeders;

use App\Models\SystemAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SystemAdmin::create([
            'name' => 'KKB System Admin',
            'email' => 'admin@kkb.test',
            'password' => Hash::make('password'),
        ]);

        $this->call(HouseholdSeeder::class);
    }
}
