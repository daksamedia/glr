<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'bio',
        'address',
        'avatar',
        'likes',
        'access_code',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'access_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
        ];
    }

    // Relationships
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getLikesArrayAttribute()
    {
        return $this->likes ? explode(',', $this->likes) : [];
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    // Methods
    public function isActive()
    {
        return $this->status == 1;
    }

    public function activate()
    {
        $this->update(['status' => true, 'email_verified_at' => now()]);
    }

    public function hasVendor()
    {
        return $this->vendor()->exists();
    }

    public function addLike($vendorId)
    {
        $likes = $this->likes_array;
        if (!in_array($vendorId, $likes)) {
            $likes[] = $vendorId;
            $this->update(['likes' => implode(',', $likes)]);
        }
    }

    public function removeLike($vendorId)
    {
        $likes = $this->likes_array;
        if (($key = array_search($vendorId, $likes)) !== false) {
            unset($likes[$key]);
            $this->update(['likes' => implode(',', array_values($likes))]);
        }
    }

    public function hasLiked($vendorId)
    {
        return in_array($vendorId, $this->likes_array);
    }

    public function generateAccessCode()
    {
        $this->access_code = bin2hex(random_bytes(16));
        $this->save();
        return $this->access_code;
    }

    public function clearAccessCode()
    {
        $this->access_code = null;
        $this->save();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
}