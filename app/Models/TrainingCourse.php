<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCourse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'short_code',
        'duration',
        'fee',
        'description',
        'status',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}