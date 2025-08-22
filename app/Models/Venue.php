<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'large_num',
        'capacity',
        'composition',
        'electricity',
        'parking_lot',
        'rooms_num',
        'toilets_num',
        'prayer_room',
        'location',
        'available_status',
        'price',
        'ratings',
        'reviews',
    ];

    protected $casts = [
        'images' => 'array',
        'electricity' => 'boolean',
        'parking_lot' => 'boolean',
        'prayer_room' => 'boolean',
        'available_status' => 'boolean',
        'price' => 'decimal:2',
        'ratings' => 'decimal:2',
    ];
}