<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'courses',
        'subscription_limit',
        'price',
        'currency',
        'status',
    ];

    protected $casts = [
        'courses' => 'array',
        'price'   => 'float',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->title) . '-' . uniqid();
            }
        });
    }
}
