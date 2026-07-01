<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('exam_type'); // UT1, UT2, UT3, Semester, Final
            $table->string('subject')->nullable();
            $table->decimal('marks', 5, 2)->nullable();
            $table->decimal('max_marks', 5, 2)->default(100);
            $table->string('grade')->nullable();
            $table->string('result_status')->default('Pass'); // Pass, Fail, Pending
            $table->string('file_path')->nullable();
            $table->text('remarks')->nullable();
            $table->date('exam_date')->nullable();
            $table->string('session')->nullable(); // e.g., 2024-25 Semester 1
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_records');
    }
};
