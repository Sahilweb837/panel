<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status',
        'attendance_date',
        'fine',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
