<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'cover',
        'bio',
        'location',
        'location_data',
        'ratings',
        'reviews',
        'price',
    ];

    protected $casts = [
        'location_data' => 'array',
        'ratings' => 'decimal:2',
        'price' => 'decimal:2',
        'reviews' => 'integer',
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

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'business_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function statistics()
    {
        return $this->hasOne(Statistic::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->ratings()->count();
    }

    public function updateRatingStatistics()
    {
        $this->ratings = $this->getAverageRatingAttribute();
        $this->reviews = $this->getTotalReviewsAttribute();
        $this->save();
    }

    // Search scope
    public function scopeSearch($query, $keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $q->where('name', 'LIKE', "%{$keywords}%")
              ->orWhere('location', 'LIKE', "%{$keywords}%")
              ->orWhere('bio', 'LIKE', "%{$keywords}%")
              ->orWhereHas('category', function ($categoryQuery) use ($keywords) {
                  $categoryQuery->where('name', 'LIKE', "%{$keywords}%");
              });
        });
    }

    // Filter by location data
    public function scopeFilterByLocation($query, $provinces = null, $cities = null)
    {
        if ($provinces) {
            $provinces = is_array($provinces) ? $provinces : explode(',', $provinces);
            $query->whereJsonContains('location_data->province', $provinces);
        }

        if ($cities) {
            $cities = is_array($cities) ? $cities : explode(',', $cities);
            $query->whereJsonContains('location_data->city', $cities);
        }

        return $query;
    }

    // Filter by category
    public function scopeFilterByCategory($query, $categoryIds)
    {
        if ($categoryIds) {
            $categoryIds = is_array($categoryIds) ? $categoryIds : explode(',', $categoryIds);
            return $query->whereIn('category_id', $categoryIds);
        }
        return $query;
    }
}