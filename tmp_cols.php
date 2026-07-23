<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pdo = DB::connection()->getPdo();
$stmt = $pdo->query('SHOW COLUMNS FROM students');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
