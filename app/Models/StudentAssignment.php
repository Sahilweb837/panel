<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'subject',
        'type',
        'due_date',
        'status',
        'priority',
        'file_path',
        'remarks',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}