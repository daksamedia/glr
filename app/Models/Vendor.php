<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'location',
        'location_data',
        'price',
        'cover',
        'category_id',
        'ratings',
        'reviews',
    ];

    protected $casts = [
        'location_data' => 'array',
        'ratings' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'business_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'business_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'vendor_id');
    }

    public function statistics()
    {
        return $this->hasOne(Statistic::class, 'business_id');
    }
}