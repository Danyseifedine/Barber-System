<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'service_id'
    ];

    /**
     * Get the appointment that owns the service.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the service that belongs to the appointment.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
