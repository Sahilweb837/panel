<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use Carbon\Carbon;

class GenerateAbsentFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-absent-fines {--date= : The date to generate fines for (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a 50 Rs fine for students who are absent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateStr = $this->option('date') ?: now()->toDateString();
        $date = Carbon::parse($dateStr);
        
        $this->info("Generating absent fines for date: {$date->toDateString()}");

        $students = Student::where('status', true)->get();
        
        $fineAmount = 50.00;
        $count = 0;

        foreach ($students as $student) {
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('attendance_date', $date->toDateString())
                ->first();

            if (!$attendance) {
                // Create absent attendance record
                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'status' => 'Absent',
                    'attendance_date' => $date->toDateString(),
                    'remarks' => 'Auto-marked absent',
                ]);
            }

            if ($attendance->status === 'Absent') {
                // Check if fine already exists for this date
                $existingFine = FeeInvoice::where('student_id', $student->id)
                    ->where('fee_category', 'Fine')
                    ->where('remarks', 'like', "%Auto-generated absent fine for {$date->toDateString()}%")
                    ->first();

                if (!$existingFine) {
                    FeeInvoice::create([
                        'student_id' => $student->id,
                        'invoice_no' => 'FIN-' . now()->format('ymdHi') . '-' . $student->id . '-' . rand(10,99),
                        'fee_category' => 'Fine',
                        'fee_items' => [['category' => 'Absent Fine', 'amount' => $fineAmount]],
                        'total_amount' => $fineAmount,
                        'paid_amount' => 0,
                        'discount' => 0,
                        'fine' => 0,
                        'due_amount' => $fineAmount,
                        'status' => 'Unpaid',
                        'remarks' => "Auto-generated absent fine for {$date->toDateString()}",
                        'created_by' => 1,
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Generated {$count} fine(s).");
    }
}
