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
            $table->text('permanent_address')->nullable()->after('address');
            $table->text('current_address')->nullable()->after('address');
            $table->string('aadhar_number', 12)->nullable()->after('roll_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['permanent_address', 'current_address', 'aadhar_number']);
        });
    }
};
