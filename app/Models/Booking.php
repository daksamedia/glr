<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'service_id',
        'user_data',
        'booking_time',
        'status',
        'notes',
        'expired_date',
    ];

    protected $casts = [
        'user_data' => 'array',
        'booking_time' => 'datetime',
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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('business_id', $vendorId);
    }

    // Accessors
    public function getFormattedBookingTimeAttribute()
    {
        return $this->booking_time->format('Y-m-d H:i:s');
    }

    public function getIsExpiredAttribute()
    {
        return $this->expired_date && Carbon::now()->gte($this->expired_date);
    }

    public function getCanBeCancelledAttribute()
    {
        return in_array($this->status, ['pending', 'confirmed']) && !$this->is_expired;
    }

    public function getCanBeRatedAttribute()
    {
        return $this->status === 'completed';
    }

    // Methods
    public function markAsExpired()
    {
        if ($this->is_expired && $this->status !== 'expired') {
            $this->update(['status' => 'expired']);
        }
    }

    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel($notes = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $notes ?: $this->notes
        ]);
    }
}