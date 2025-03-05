<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = [
        'appointment_id',
        'amount',
    ];

    /**
     * Get the appointment that owns the payment.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
