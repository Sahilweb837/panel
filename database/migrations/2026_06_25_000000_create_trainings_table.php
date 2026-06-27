<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slip_no', 60)->unique();
            $table->string('name', 100);
            $table->string('father_name', 100)->nullable();
            $table->string('email', 100);
            $table->string('college', 150)->nullable();
            $table->string('mobile', 20);
            $table->unsignedBigInteger('training_course_id');
            $table->string('duration', 50);
            $table->decimal('fees', 10, 2)->default(0.00);
            $table->string('payment_method', 50)->default('Cash');
            $table->date('payment_date');
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('training_course_id', 'trainings_training_course_id_foreign')
                ->references('id')->on('training_courses')
                ->onDelete('cascade');

            $table->foreign('created_by', 'trainings_created_by_foreign')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
