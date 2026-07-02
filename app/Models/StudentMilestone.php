<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'title',
        'description',
        'milestone_type',
        'target_date',
        'status',
        'priority',
        'source',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
