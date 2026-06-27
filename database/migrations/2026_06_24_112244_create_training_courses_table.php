<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('short_code', 50)->nullable();
            $table->string('duration', 50)->default('28 Days');
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->decimal('tenure_1_month', 10, 2)->default(0.00);
            $table->decimal('tenure_3_months', 10, 2)->default(0.00);
            $table->decimal('tenure_6_months', 10, 2)->default(0.00);
            $table->decimal('tenure_12_months', 10, 2)->default(0.00);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique('name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_courses');
    }
};
