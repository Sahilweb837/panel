<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'invoice_no',
        'invoice_items',
        'subtotal',
        'tax_percent',
        'tax_amount',
        'discount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'due_date',
        'payment_date',
        'payment_method',
        'transaction_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_items' => 'array',
        'due_date'      => 'date',
        'payment_date'  => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
