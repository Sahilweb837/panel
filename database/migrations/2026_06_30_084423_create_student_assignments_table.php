<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject')->nullable();
            $table->string('type')->default('Assignment'); // Assignment, Project, Lab, etc.
            $table->date('due_date')->nullable();
            $table->string('status')->default('Pending'); // Pending, In Progress, Submitted, Completed, Overdue
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->string('file_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assignments');
    }
};
