<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        User::create([
            'name' => 'Leila Admin',
            'email' => 'leila@example.com',
            'password' => Hash::make('admin'),
        ]);

        // Seed clients, services, availabilities, and appointments
        $this->call([
            ClientSeeder::class,         // Create clients first so appointments can reference them
            ServiceSeeder::class,        // Create services first so appointments can reference them
            AvailabilitySeeder::class,   // Create availabilities next to have slots for appointments
            AppointmentSeeder::class,    // Create appointments last since they depend on clients and availabilities
        ]);
    }
}
