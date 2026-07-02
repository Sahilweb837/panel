<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffIncomeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'income_type',
        'amount',
        'payment_method',
        'reference_no',
        'description',
        'income_date',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
