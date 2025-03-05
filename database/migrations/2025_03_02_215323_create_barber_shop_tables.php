<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add customer fields to users table
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 15)->nullable();
            }
            if (!Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('users', 'registration_date')) {
                $table->timestamp('registration_date')->useCurrent();
            }
        });

        // Services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name', 100);
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });

        // Appointments table
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Changed from customer_id to user_id
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Appointment services (many-to-many relationship)
        Schema::create('appointment_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->timestamps();
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Business hours
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        // Insert sample data for business hours
        DB::table('business_hours')->insert([
            ['day_of_week' => 'Monday', 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['day_of_week' => 'Tuesday', 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['day_of_week' => 'Wednesday', 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['day_of_week' => 'Thursday', 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['day_of_week' => 'Friday', 'open_time' => '09:00:00', 'close_time' => '18:00:00', 'is_closed' => false],
            ['day_of_week' => 'Saturday', 'open_time' => null, 'close_time' => null, 'is_closed' => true],
            ['day_of_week' => 'Sunday', 'open_time' => null, 'close_time' => null, 'is_closed' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('appointment_services');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('services');
        Schema::dropIfExists('business_hours');

        // Remove added columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('users', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('users', 'registration_date')) {
                $table->dropColumn('registration_date');
            }
        });
    }
};
