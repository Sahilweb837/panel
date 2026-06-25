<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slip_no',
        'name',
        'father_name',
        'email',
        'college',
        'mobile',
        'course_id',
        'duration',
        'fees',
        'payment_method',
        'upi_transaction_id',
        'payment_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'fees' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
