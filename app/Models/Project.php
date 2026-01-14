<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'year',
        'services',
        'images',
    ];

    protected $casts = [
        'services' => 'array',
        'images' => 'array',
    ];
}
