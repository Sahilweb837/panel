<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'milestone_title',
        'description',
        'order_index',
        'is_completed',
        'completed_at',
        'covered_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
