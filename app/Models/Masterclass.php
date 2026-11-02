<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Masterclass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'masterclasses';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'title',
        'slug',
        'description',
        'mentor_name',
        'scheduled_at',
        'duration_minutes',
        'meeting_link',
        'recording_url',
        'status',
        'max_participants',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) {
                $m->slug = Str::slug($m->title) . '-' . mt_rand(100, 999);
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}

