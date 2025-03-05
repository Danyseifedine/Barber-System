<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointmentServices()
    {
        return $this->hasMany(AppointmentService::class);
    }
    /**
     * The services that belong to the appointment.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
            ->withTimestamps();
    }

    /**
     * Get the payment associated with the appointment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the total price of all services for this appointment.
     */
    public function getTotalPriceAttribute()
    {
        return $this->services->sum('price');
    }

    /**
     * Get the total duration of all services for this appointment.
     */
    public function getTotalDurationAttribute()
    {
        return $this->services->sum('duration_minutes');
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now()->format('Y-m-d'))
            ->orWhere('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled appointments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
