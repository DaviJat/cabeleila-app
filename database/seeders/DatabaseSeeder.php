<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin
        User::factory()->create([
            'name' => 'Leila Admin',
            'email' => 'leila@example.com',
            'role' => 'admin',
        ]);

        // 2. Create Clients
        Client::factory(20)->create();

        // 3. Call modular seeders in the correct order
        $this->call([
            ServiceSeeder::class,        // Create services first so appointments can reference them
            AvailabilitySeeder::class,   // Create availabilities next to have slots for appointments
            AppointmentSeeder::class,    // Create appointments last since they depend on clients and availabilities
        ]);
    }
}
