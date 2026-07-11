<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateMissingUserAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-missing-user-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates User accounts for Students and Staff that do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting to generate missing user accounts...");

        // Generate for Students
        $studentRole = Role::where('slug', 'student')->first();
        if (!$studentRole) {
            $this->error("Student role not found. Please ensure roles are seeded.");
            return;
        }

        $students = Student::whereNull('user_id')->get();
        $studentCount = 0;
        foreach ($students as $student) {
            $username = strtolower(str_replace(' ', '', $student->first_name)) . $student->admission_no;
            $email = $student->email ?: ($student->admission_no . '@student.com');
            $plainPassword = $student->dob ? \Carbon\Carbon::parse($student->dob)->format('dmY') : $student->admission_no;
            
            // Ensure unique email/username
            $existingUser = User::where('email', $email)->orWhere('username', $username)->first();
            if ($existingUser) {
                // If user exists but is not linked, just link it
                $student->user_id = $existingUser->id;
                $student->save();
                $studentCount++;
                continue;
            }

            $user = User::create([
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($plainPassword),
                'raw_password' => $plainPassword,
                'role_id' => $studentRole->id,
                'status' => true,
            ]);

            $student->user_id = $user->id;
            $student->save();
            $studentCount++;
        }
        $this->info("Created/Linked user accounts for {$studentCount} students.");

        // Generate for Staff (Employees)
        $staffRole = Role::where('slug', 'staff')->first();
        if (!$staffRole) {
            $this->error("Staff role not found. Please ensure roles are seeded.");
            return;
        }

        $employees = Employee::whereNull('user_id')->get();
        $staffCount = 0;
        foreach ($employees as $employee) {
            $email = strtolower($employee->employee_code) . '@staff.com';
            $username = strtolower($employee->employee_code);
            $plainPassword = 'staff123';

            $existingUser = User::where('email', $email)->orWhere('username', $username)->first();
            if ($existingUser) {
                $employee->user_id = $existingUser->id;
                $employee->save();
                $staffCount++;
                continue;
            }

            $user = User::create([
                'name' => $employee->staff_name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($plainPassword),
                'raw_password' => $plainPassword,
                'role_id' => $staffRole->id,
                'status' => true,
            ]);

            $employee->user_id = $user->id;
            $employee->save();
            $staffCount++;
        }
        $this->info("Created/Linked user accounts for {$staffCount} staff members.");

        $this->info("Done!");
    }
}
