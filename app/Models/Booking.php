<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_data',
        'business_id',
        'service_id',
        'booking_time',
        'status',
        'notes',
        'expired_date',
    ];

    protected $casts = [
        'booking_time' => 'array',
        'user_data' => 'array',
        'expired_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'business_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}