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
        try {
            $database = env('DB_DATABASE');
            $date = now()->format('Y-m-d_H-i-s');
            $filename = "backup-{$database}-{$date}.sql";
            $storagePath = storage_path("app/backups");

            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filePath = "{$storagePath}/{$filename}";

            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$database}";

            foreach ($tables as $tableRow) {
                // Determine the correct property name for the table dynamically
                $tableName = null;
                foreach ((array)$tableRow as $key => $val) {
                    if (strpos($key, 'Tables_in_') === 0) {
                        $tableName = $val;
                        break;
                    }
                }
                
                if (!$tableName) {
                    continue; // Skip if we can't find table name
                }

                $createStmt = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createStmt->{'Create Table'} . ";\n\n";

                $rows = \Illuminate\Support\Facades\DB::select("SELECT * FROM `{$tableName}`");
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($row as $val) {
                            if (is_null($val)) {
                                $values[] = "NULL";
                            } else {
                                $val = addslashes($val);
                                $val = str_replace("\n", "\\n", $val);
                                $values[] = "'{$val}'";
                            }
                        }
                        $sql .= "INSERT INTO `{$tableName}` VALUES(" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($filePath, $sql);

            $this->info("Database backup created successfully: {$filename}");
        } catch (\Exception $e) {
            $this->error("Failed to backup database. Error: " . $e->getMessage());
        }
    }
}
