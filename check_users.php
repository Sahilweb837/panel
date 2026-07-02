<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== USERS ===\n";
foreach (DB::table('users')->get(['id','name','email','role_id','status']) as $u) {
    echo $u->id . ': ' . $u->name . ' | ' . $u->email . ' | role_id=' . $u->role_id . ' | status=' . $u->status . "\n";
}

echo "\n=== ROLES ===\n";
foreach (DB::table('roles')->get(['id','role_name','slug']) as $r) {
    echo $r->id . ': ' . $r->role_name . ' | ' . $r->slug . "\n";
}

echo "\n=== EMPLOYEES ===\n";
foreach (DB::table('employees')->get(['id','user_id','employee_code','designation']) as $e) {
    echo $e->id . ': user_id=' . $e->user_id . ' | code=' . $e->employee_code . ' | ' . $e->designation . "\n";
}

echo "\n=== STUDENTS ===\n";
foreach (DB::table('students')->get(['id','user_id','admission_no','first_name','last_name']) as $s) {
    echo $s->id . ': user_id=' . $s->user_id . ' | ' . $s->admission_no . ' | ' . $s->first_name . ' ' . $s->last_name . "\n";
}
