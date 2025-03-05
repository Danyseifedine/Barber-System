<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator',

        ]);

        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'User',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ])->addRole('admin');

        $this->call([
            // Other seeders you might have
            BarberServicesSeeder::class,
            UsersAndAppointmentsSeeder::class,
        ]);
    }
}
