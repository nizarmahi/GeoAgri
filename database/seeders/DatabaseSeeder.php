<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@geoagri.id'],
            [
                'name' => 'Admin GeoAgri',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Guest / test user
        User::firstOrCreate(
            ['email' => 'guest@geoagri.id'],
            [
                'name' => 'Guest User',
                'password' => Hash::make('password'),
                'role' => 'guest',
            ]
        );
    }
}
