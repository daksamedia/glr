<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'url',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'business_id');
    }
}