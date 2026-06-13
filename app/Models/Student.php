<?php

namespace App\Models;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\FeeInvoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_no',
        'roll_no',
        'aadhar_number',
        'first_name',
        'last_name',
        'guardian_name',
        'email',
        'phone',
        'dob',
        'gender',
        'address',
        'current_address',
        'permanent_address',
        'course_id',
        'course_duration',
        'student_type',
        'class',
        'section',
        'admission_date',
        'status',
        'biometric_id',
        'discount',
        'registration_fee',
        'prospectus_fee',
        'fee_tenure',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function feeInvoices()
    {
        return $this->hasMany(FeeInvoice::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
