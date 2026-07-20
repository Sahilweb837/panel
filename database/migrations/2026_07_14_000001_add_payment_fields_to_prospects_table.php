<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Ensure the prospects table exists before adding payment fields.
    if (!Schema::hasTable('prospects')) {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    Schema::table('prospects', function (Blueprint $table) {
        $table->decimal('paid_amount', 10, 2)->default(0);
        $table->decimal('fine_total', 10, 2)->default(0);
        $table->decimal('total_due', 10, 2)->default(0);
        $table->decimal('remaining_balance', 10, 2)->default(0);
        $table->timestamp('payment_date')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'fine_total', 'total_due', 'remaining_balance', 'payment_date']);
        });
    }
};
?>
