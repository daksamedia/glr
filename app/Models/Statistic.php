<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'views',
        'likes',
        'orders',
    ];

    protected $casts = [
        'views' => 'integer',
        'likes' => 'integer',
        'orders' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'business_id');
    }
}