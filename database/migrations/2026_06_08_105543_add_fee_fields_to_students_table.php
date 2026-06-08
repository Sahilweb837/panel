<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('status');
            $table->decimal('registration_fee', 10, 2)->default(500)->after('discount');
            $table->decimal('prospectus_fee', 10, 2)->default(5000)->after('registration_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['discount', 'registration_fee', 'prospectus_fee']);
        });
    }
};
