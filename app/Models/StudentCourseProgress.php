<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourseProgress extends Model
{
    use HasFactory;

    protected $table = 'student_course_progress';

    protected $fillable = [
        'student_id',
        'course_id',
        'cohort_id',
        'completed_lessons',
        'total_lessons',
        'progress_percentage',
        'grade',
        'is_certified',
        'certified_at',
        'last_active_at',
    ];

    protected $casts = [
        'completed_lessons' => 'integer',
        'total_lessons' => 'integer',
        'progress_percentage' => 'float',
        'is_certified' => 'boolean',
        'certified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function cohort()
    {
        return $this->belongsTo(Cohort::class);
    }
}

