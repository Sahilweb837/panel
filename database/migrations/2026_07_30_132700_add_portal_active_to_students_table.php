<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'portal_active')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('portal_active')->default(true)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'portal_active')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('portal_active');
            });
        }
    }
};
