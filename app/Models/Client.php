<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'address',
        'gst_no',
        'pan_no',
        'status',
        'notes',
    ];

    public function invoices()
    {
        return $this->hasMany(ClientInvoice::class);
    }
}
