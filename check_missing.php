<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Employee;

echo "=== STUDENTS WITHOUT USER_ID ===\n";
foreach (Student::whereNull('user_id')->get(['id','admission_no','first_name','last_name']) as $s) {
    echo $s->id . ': ' . $s->first_name . ' ' . $s->last_name . ' (' . $s->admission_no . ')' . "\n";
}

echo "\n=== EMPLOYEES WITHOUT USER_ID ===\n";
foreach (Employee::whereNull('user_id')->get(['id','employee_code','designation']) as $e) {
    echo $e->id . ': code=' . $e->employee_code . ' | ' . $e->designation . "\n";
}
