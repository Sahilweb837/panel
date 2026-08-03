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
        Schema::table('daily_updates', function (Blueprint $table) {
            $table->string('work_title')->nullable()->after('employee_id');
            $table->string('file_path')->nullable()->after('update_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_updates', function (Blueprint $table) {
            $table->dropColumn(['work_title', 'file_path']);
        });
    }
};
