<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersAndAppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 4 regular users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '555-123-4567',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '555-234-5678',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '555-345-6789',
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'phone_number' => '555-456-7890',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->addRole('user');
            $createdUsers[] = $user;
        }

        // Get all services
        $services = Service::all();

        // Create 10 appointments with different statuses
        $statuses = ['scheduled', 'completed', 'cancelled'];
        $today = Carbon::today();

        // Create appointments with different dates and statuses
        for ($i = 0; $i < 10; $i++) {
            $user = $createdUsers[array_rand($createdUsers)];
            $status = $statuses[array_rand($statuses)];

            // Set date based on status
            if ($status === 'completed') {
                $date = $today->copy()->subDays(rand(1, 30));
            } elseif ($status === 'cancelled') {
                $date = $today->copy()->addDays(rand(-15, 15));
            } else { // scheduled
                $date = $today->copy()->addDays(rand(1, 30));
            }

            // Create random start time between 9 AM and 5 PM
            $hour = rand(9, 16);
            $minute = [0, 15, 30, 45][rand(0, 3)];
            $startTime = sprintf('%02d:%02d:00', $hour, $minute);

            // Select 1-3 random services
            $appointmentServices = $services->random(rand(1, 3));
            $totalDuration = $appointmentServices->sum('duration_minutes');

            // Calculate end time
            $endTime = Carbon::createFromFormat('H:i:s', $startTime)
                ->addMinutes($totalDuration)
                ->format('H:i:s');

            // Create the appointment
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'appointment_date' => $date->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'notes' => $status === 'cancelled' ? 'Customer cancelled due to scheduling conflict' : null,
            ]);

            // Attach services to the appointment
            foreach ($appointmentServices as $service) {
                $appointment->services()->attach($service->id);
            }

            // Create payment for completed appointments
            if ($status === 'completed') {
                $totalAmount = $appointmentServices->sum('price');

                // Add a small random tip (0-20% of the total)
                $tipPercentage = rand(0, 20) / 100;
                $tipAmount = $totalAmount * $tipPercentage;
                $finalAmount = $totalAmount + $tipAmount;

                Payment::create([
                    'appointment_id' => $appointment->id,
                    'amount' => round($finalAmount, 2),
                    'created_at' => $date->copy()->addHours(rand(1, 3)),
                    'updated_at' => $date->copy()->addHours(rand(1, 3)),
                ]);
            }
        }
    }
}
