<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;

$employees = Employee::whereNull('user_id')->get();
echo "Found " . $employees->count() . " employees with null user_id.\n";

$staffRole = Role::where('slug', 'staff')->first();

foreach ($employees as $employee) {
    echo "Fixing employee: " . $employee->employee_code . "... ";
    
    // Create a User record
    $email = strtolower($employee->employee_code) . '@staff.com';
    $username = strtolower($employee->employee_code);
    
    // Check if user already exists with this email or username
    $existingUser = User::where('email', $email)->orWhere('username', $username)->first();
    if ($existingUser) {
        $employee->user_id = $existingUser->id;
        $employee->save();
        echo "Linked to existing user ID: " . $existingUser->id . "\n";
        continue;
    }
    
    $user = User::create([
        'name' => $employee->employee_code, // fallback name
        'email' => $email,
        'username' => $username,
        'password' => \Illuminate\Support\Facades\Hash::make('staff123'),
        'role_id' => $staffRole?->id,
        'status' => $employee->status,
    ]);
    
    $employee->user_id = $user->id;
    $employee->save();
    echo "Created user ID: " . $user->id . "\n";
}

echo "Done.\n";
