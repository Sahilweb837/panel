<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->boolean('is_phone_verified')->default(false)->after('phone_number');
            $table->string('firebase_uid')->nullable()->after('is_phone_verified');
            $table->timestamp('phone_verified_at')->nullable()->after('firebase_uid');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'is_phone_verified', 'firebase_uid', 'phone_verified_at']);
        });
    }
};
