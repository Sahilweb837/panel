<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_title',
        'update_text',
        'file_path',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
