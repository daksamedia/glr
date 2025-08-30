<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'amenities',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'capacity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw("
            *, (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<', $radius)
        ->orderBy('distance');
    }

    // Accessors
    public function getHasCoordinatesAttribute()
    {
        return $this->latitude && $this->longitude;
    }

    public function getFormattedCapacityAttribute()
    {
        return $this->capacity ? number_format($this->capacity) . ' people' : null;
    }

    public function getAmenitiesListAttribute()
    {
        return $this->amenities ? implode(', ', $this->amenities) : '';
    }

    public function getGoogleMapsLinkAttribute()
    {
        if (!$this->has_coordinates) return null;
        
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    // Methods
    public function distanceTo($latitude, $longitude)
    {
        if (!$this->has_coordinates) return null;

        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function addAmenity($amenity)
    {
        $amenities = $this->amenities ?? [];
        
        if (!in_array($amenity, $amenities)) {
            $amenities[] = $amenity;
            $this->update(['amenities' => $amenities]);
        }
    }

    public function removeAmenity($amenity)
    {
        $amenities = $this->amenities ?? [];
        
        if (($key = array_search($amenity, $amenities)) !== false) {
            unset($amenities[$key]);
            $this->update(['amenities' => array_values($amenities)]);
        }
    }
}