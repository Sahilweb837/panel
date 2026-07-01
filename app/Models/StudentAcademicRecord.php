<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAcademicRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'exam_type',
        'subject',
        'marks',
        'max_marks',
        'grade',
        'result_status',
        'file_path',
        'remarks',
        'exam_date',
        'session',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'marks' => 'decimal:2',
        'max_marks' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}