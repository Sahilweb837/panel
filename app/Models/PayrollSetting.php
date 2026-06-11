<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = [
        'razorpay_key_id',
        'razorpay_key_secret',
        'razorpay_account_number',
        'mode',
    ];

    /**
     * Always return the single settings row, or create one.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate([], ['mode' => 'test']);
    }

    /**
     * Decrypt the stored secret for API use.
     */
    public function getDecryptedSecret(): ?string
    {
        if (!$this->razorpay_key_secret) return null;
        try {
            return decrypt($this->razorpay_key_secret);
        } catch (\Exception $e) {
            return null;
        }
    }
}
