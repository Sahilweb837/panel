<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cohort extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cohorts';

    protected $fillable = [
        'course_id',
        'mentor_id',
        'name',
        'slug',
        'start_date',
        'end_date',
        'schedule',
        'capacity',
        'status',
        'is_published',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'capacity' => 'integer',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cohort) {
            if (empty($cohort->slug)) {
                $cohort->slug = Str::slug($cohort->name) . '-' . mt_rand(100, 999);
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function progressRecords()
    {
        return $this->hasMany(StudentCourseProgress::class, 'cohort_id');
    }

    public function getEnrolledCountAttribute()
    {
        return $this->progressRecords()->count();
    }
}

