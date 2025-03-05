<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_name',
        'description',
        'duration_minutes',
        'price',
        'status'
    ];

    /**
     * The appointments that belong to the service.
     */
    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_services')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include available services.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}
