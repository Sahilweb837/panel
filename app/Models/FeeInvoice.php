<?php

namespace App\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'invoice_no',
        'fee_category',
        'fee_items',
        'total_amount',
        'paid_amount',
        'discount',
        'fine',
        'due_amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'utr_no',
        'status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'fee_items' => 'array',
        'payment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
