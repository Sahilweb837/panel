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
        if (!Schema::hasTable('cohorts')) {
            Schema::create('cohorts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->unsignedBigInteger('mentor_id')->nullable()->index();
                $table->string('name');
                $table->string('slug')->unique();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('schedule')->nullable();
                $table->integer('capacity')->default(30);
                $table->string('status', 30)->default('open'); // draft, open, ongoing, completed
                $table->boolean('is_published')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('masterclasses')) {
            Schema::create('masterclasses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->nullable()->index();
                $table->unsignedBigInteger('instructor_id')->nullable()->index();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('mentor_name')->nullable();
                $table->dateTime('scheduled_at')->nullable();
                $table->integer('duration_minutes')->default(60);
                $table->string('meeting_link', 500)->nullable();
                $table->string('recording_url', 500)->nullable();
                $table->string('status', 30)->default('published'); // draft, published, live, completed
                $table->integer('max_participants')->default(100);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('student_course_progress')) {
            Schema::create('student_course_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('course_id')->index();
                $table->unsignedBigInteger('cohort_id')->nullable()->index();
                $table->integer('completed_lessons')->default(0);
                $table->integer('total_lessons')->default(50);
                $table->decimal('progress_percentage', 5, 2)->default(0.00);
                $table->string('grade', 10)->nullable();
                $table->boolean('is_certified')->default(false);
                $table->dateTime('certified_at')->nullable();
                $table->dateTime('last_active_at')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'course_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_progress');
        Schema::dropIfExists('masterclasses');
        Schema::dropIfExists('cohorts');
    }
};

