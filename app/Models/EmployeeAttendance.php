<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $table = 'employee_attendances';

    protected $fillable = [
        'employee_id',
        'status',
        'check_in_time',
        'check_out_time',
        'attendance_date',
        'remarks',
        'photo_path',
        'device_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
