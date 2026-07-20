<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'duration',
        'fee',
        'status',
        'syllabus_path',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function milestones()
    {
        return $this->hasMany(CourseMilestone::class)->orderBy('order_index')->orderBy('id');
    }
}
