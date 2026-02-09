<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'year',
        'services',
        'images',
        'order',
    ];

    protected $casts = [
        'services' => 'array',
        'images' => 'array',
    ];

    public function getImageUrlsAttribute()
    {
        if (empty($this->images)) {
            return [];
        }

        $baseUrl = config('app.url');

        return collect($this->images)->map(function ($path) use ($baseUrl) {
            return $baseUrl . Storage::url($path);
        })->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            if (!empty($project->images) && is_array($project->images)) {
                foreach ($project->images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
        });
    }
}
