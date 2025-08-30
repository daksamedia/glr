<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'views',
        'likes',
        'bookings_count',
        'total_earnings',
    ];

    protected $casts = [
        'views' => 'integer',
        'likes' => 'integer',
        'bookings_count' => 'integer',
        'total_earnings' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Methods to increment statistics
    public function incrementViews($amount = 1)
    {
        $this->increment('views', $amount);
    }

    public function incrementLikes($amount = 1)
    {
        $this->increment('likes', $amount);
    }

    public function decrementLikes($amount = 1)
    {
        $this->decrement('likes', $amount);
    }

    public function incrementBookings($amount = 1)
    {
        $this->increment('bookings_count', $amount);
    }

    public function addEarnings($amount)
    {
        $this->increment('total_earnings', $amount);
    }

    // Accessors
    public function getFormattedEarningsAttribute()
    {
        return number_format($this->total_earnings, 2);
    }

    public function getEngagementRateAttribute()
    {
        if ($this->views == 0) return 0;
        return round(($this->likes / $this->views) * 100, 2);
    }

    public function getConversionRateAttribute()
    {
        if ($this->views == 0) return 0;
        return round(($this->bookings_count / $this->views) * 100, 2);
    }

    // Create or update statistics
    public static function updateOrCreateForVendor($vendorId, $data = [])
    {
        return static::updateOrCreate(
            ['vendor_id' => $vendorId],
            $data
        );
    }
}