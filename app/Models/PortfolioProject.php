<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioProject extends Model
{
    protected $fillable = [
        'original_url',
        'title',
        'description',
        'image_url',
        'status',
    ];
}
