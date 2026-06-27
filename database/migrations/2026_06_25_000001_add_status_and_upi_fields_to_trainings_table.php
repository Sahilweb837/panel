<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('status', 20)->default('Unpaid')->after('payment_date');
            $table->string('upi_transaction_id', 100)->nullable()->after('status');
            $table->string('course_name', 150)->nullable()->after('training_course_id');
            $table->unsignedBigInteger('training_course_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['status', 'upi_transaction_id', 'course_name']);
            $table->unsignedBigInteger('training_course_id')->nullable(false)->change();
        });
    }
};
