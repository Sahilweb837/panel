<?php

namespace App\Models;

use App\Models\SalarySlip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'phone',
        'department',
        'designation',
        'salary',
        'joining_date',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }
}
