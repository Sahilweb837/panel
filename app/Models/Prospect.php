<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'registration_fee',
        'monthly_fee',
        'is_paid',
        'is_active',
        'paid_amount',
        'fine_total',
        'total_due',
        'remaining_balance',
        'payment_date',
    ];

    protected $casts = [
        'is_paid'           => 'boolean',
        'is_active'         => 'boolean',
        'payment_date'      => 'datetime',
        'registration_fee'  => 'decimal:2',
        'monthly_fee'       => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'fine_total'        => 'decimal:2',
        'total_due'         => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    /**
     * Re-calculate total_due and remaining_balance.
     * Call this whenever registration_fee, monthly_fee, fine_total, or paid_amount changes.
     */
    public function recalculate(): void
    {
        $this->total_due        = $this->registration_fee + $this->monthly_fee + $this->fine_total;
        $this->remaining_balance = max(0, $this->total_due - $this->paid_amount);
        $this->saveQuietly();
    }
}
?>
