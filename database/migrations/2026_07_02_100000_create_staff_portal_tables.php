<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_offer_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('offer_letter_no')->unique();
            $table->string('designation');
            $table->string('department')->nullable();
            $table->decimal('offered_salary', 10, 2);
            $table->date('joining_date');
            $table->date('valid_until')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('Pending'); // Pending, Accepted, Rejected, Expired
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('leave_type'); // Casual, Sick, Earned, Maternity, Paternity, Other
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Cancelled
            $table->text('admin_remarks')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('milestone_type')->default('Milestone'); // Milestone, Assessment, Project, Lab, Seminar
            $table->date('target_date')->nullable();
            $table->string('status')->default('Upcoming'); // Upcoming, In Progress, Completed, Skipped
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->string('source')->default('Syllabus'); // Syllabus, Course Outline, Custom
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('staff_income_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('income_type'); // Salary, Bonus, Incentive, Reimbursement, Other
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // Cash, Bank Transfer, UPI, Cheque
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->date('income_date');
            $table->string('status')->default('Received'); // Pending, Received, Failed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_income_records');
        Schema::dropIfExists('student_milestones');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('staff_offer_letters');
    }
};
