<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('raw_password')->nullable()->after('password');
        });

        // Set default raw_password for existing users based on role
        DB::table('users')->where('role_id', 2)->update(['raw_password' => 'staff123']);
        
        // For students (role_id = 3), we can't reliably guess the DOB here, so let's set a generic string or leave it.
        // Actually, we can join with students table to set it to admission_no
        DB::statement("UPDATE users u INNER JOIN students s ON u.id = s.user_id SET u.raw_password = s.admission_no WHERE u.role_id = 3");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('raw_password');
        });
    }
};
