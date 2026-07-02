<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffOfferLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'offer_letter_no',
        'designation',
        'department',
        'offered_salary',
        'joining_date',
        'valid_until',
        'file_path',
        'status',
        'remarks',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
