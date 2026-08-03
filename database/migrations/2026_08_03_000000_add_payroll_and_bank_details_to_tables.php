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
        // 1. Create payroll_settings table if not exists
        if (!Schema::hasTable('payroll_settings')) {
            Schema::create('payroll_settings', function (Blueprint $table) {
                $table->id();
                $table->string('razorpay_key_id', 100)->nullable();
                $table->text('razorpay_key_secret')->nullable();
                $table->string('razorpay_account_number', 50)->nullable();
                $table->enum('mode', ['test', 'live'])->default('test');
                $table->timestamps();
            });

            // Insert default row
            \DB::table('payroll_settings')->insert([
                'mode' => 'test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Add bank columns to employees table if not exists
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'bank_account_no')) {
                $table->string('bank_account_no', 30)->nullable()->after('biometric_id');
            }
            if (!Schema::hasColumn('employees', 'bank_ifsc')) {
                $table->string('bank_ifsc', 15)->nullable()->after('bank_account_no');
            }
            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('bank_ifsc');
            }
            if (!Schema::hasColumn('employees', 'account_holder_name')) {
                $table->string('account_holder_name', 150)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('employees', 'razorpay_contact_id')) {
                $table->string('razorpay_contact_id', 100)->nullable()->after('account_holder_name');
            }
            if (!Schema::hasColumn('employees', 'razorpay_fund_account_id')) {
                $table->string('razorpay_fund_account_id', 100)->nullable()->after('razorpay_contact_id');
            }
        });

        // 3. Add payout tracking columns to salary_slips table if not exists
        Schema::table('salary_slips', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_slips', 'razorpay_payout_id')) {
                $table->string('razorpay_payout_id', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('salary_slips', 'payout_status')) {
                $table->string('payout_status', 50)->nullable()->after('razorpay_payout_id');
            }
            if (!Schema::hasColumn('salary_slips', 'payout_mode')) {
                $table->string('payout_mode', 20)->nullable()->after('payout_status');
            }
            if (!Schema::hasColumn('salary_slips', 'payout_initiated_at')) {
                $table->timestamp('payout_initiated_at')->nullable()->after('payout_mode');
            }
            if (!Schema::hasColumn('salary_slips', 'payout_response')) {
                $table->text('payout_response')->nullable()->after('payout_initiated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_payout_id',
                'payout_status',
                'payout_mode',
                'payout_initiated_at',
                'payout_response',
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'bank_account_no',
                'bank_ifsc',
                'bank_name',
                'account_holder_name',
                'razorpay_contact_id',
                'razorpay_fund_account_id',
            ]);
        });

        Schema::dropIfExists('payroll_settings');
    }
};
