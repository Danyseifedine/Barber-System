<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarberServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'Classic Haircut',
                'description' => 'Traditional haircut with scissors and clippers, includes wash and styling.',
                'duration_minutes' => 30,
                'price' => 25.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Beard Trim',
                'description' => 'Precision beard trimming and shaping to maintain your style.',
                'duration_minutes' => 20,
                'price' => 15.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Hot Towel Shave',
                'description' => 'Traditional straight razor shave with hot towel treatment.',
                'duration_minutes' => 30,
                'price' => 30.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Haircut & Beard Combo',
                'description' => 'Complete package including haircut and beard trim.',
                'duration_minutes' => 45,
                'price' => 35.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Hair Coloring',
                'description' => 'Professional hair coloring service to cover gray or change your look.',
                'duration_minutes' => 60,
                'price' => 50.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Kids Haircut',
                'description' => 'Haircut service for children under 12.',
                'duration_minutes' => 20,
                'price' => 18.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Head Shave',
                'description' => 'Complete head shave with razor, includes scalp treatment.',
                'duration_minutes' => 25,
                'price' => 22.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Facial Treatment',
                'description' => 'Cleansing facial treatment with exfoliation and moisturizing.',
                'duration_minutes' => 40,
                'price' => 40.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Hair & Scalp Treatment',
                'description' => 'Deep conditioning treatment for hair and scalp health.',
                'duration_minutes' => 35,
                'price' => 28.00,
                'status' => 'available',
            ],
            [
                'service_name' => 'Deluxe Package',
                'description' => 'Premium service including haircut, beard trim, facial, and scalp massage.',
                'duration_minutes' => 90,
                'price' => 75.00,
                'status' => 'available',
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'service_name' => $service['service_name'],
                'description' => $service['description'],
                'duration_minutes' => $service['duration_minutes'],
                'price' => $service['price'],
                'status' => $service['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
