<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    protected $description = 'Backup the database using mysqldump';

    public function handle()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $date = now()->format('Y-m-d_H-i-s');
        $filename = "backup-{$database}-{$date}.sql";
        $storagePath = storage_path("app/backups");

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = "{$storagePath}/{$filename}";

        $passwordParam = $password ? "-p{$password}" : "";
        
        // Use mysqldump directly assuming it is in PATH
        $command = "mysqldump --user={$username} {$passwordParam} --host={$host} {$database} > \"{$filePath}\" 2>&1";

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Database backup created successfully: {$filename}");
        } else {
            $this->error("Failed to backup database. Error code: {$returnVar}");
            $this->error("Output: " . implode("\n", $output));
            
            // Try explicit xampp path if mysqldump is not in PATH
            $xamppCommand = "d:\\xammp1\\mysql\\bin\\mysqldump.exe --user={$username} {$passwordParam} --host={$host} {$database} > \"{$filePath}\" 2>&1";
            $this->info("Trying alternative path: {$xamppCommand}");
            
            exec($xamppCommand, $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->info("Database backup created successfully using alternative path: {$filename}");
            } else {
                $this->error("Failed to backup database with alternative path either. Error code: {$returnVar}");
            }
        }
    }
}
