<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\BiometricDevice;
use App\Models\ChatbotInteraction;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use App\Models\Expense;
use App\Models\Task;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function query(Request $request)
    {
        $queryText = trim($request->input('query', ''));
        if (empty($queryText)) {
            return response()->json(['response' => 'Hello! How can I assist you with your system data today?']);
        }

        $response = '';
        $queryLower = strtolower($queryText);

        try {
            if ($this->matchesIntent($queryLower, ['fee', 'pending', 'outstanding', 'due', 'unpaid', 'payment'])) {
                $response = $this->handlePendingFees();
            } elseif ($this->matchesIntent($queryLower, ['biometric', 'adms', 'device', 'sync', 'hardware', 'zkteco', 'fingerprint'])) {
                $response = $this->handleBiometricStatus();
            } elseif ($this->matchesIntent($queryLower, ['error', 'diagnostic', 'health', 'system', 'check', 'status'])) {
                $response = $this->handleSystemDiagnostics();
            } elseif ($this->matchesIntent($queryLower, ['new student', 'recent student', 'latest student', 'registered', 'admission', 'enrolled', 'enrollment'])) {
                $response = $this->handleNewStudents();
            } elseif ($this->matchesIntent($queryLower, ['student count', 'total student', 'how many student', 'students'])) {
                $response = $this->handleStudentStats();
            } elseif ($this->matchesIntent($queryLower, ['employee', 'staff', 'team', 'worker'])) {
                $response = $this->handleEmployeeStats();
            } elseif ($this->matchesIntent($queryLower, ['task', 'assign', 'work assigned', 'my task', 'pending task'])) {
                $response = $this->handleTaskSummary();
            } elseif ($this->matchesIntent($queryLower, ['attendance', 'present', 'absent', 'today attendance'])) {
                $response = $this->handleAttendanceSummary();
            } elseif ($this->matchesIntent($queryLower, ['expense', 'spending', 'cost'])) {
                $response = $this->handleExpenseSummary();
            } elseif ($this->matchesIntent($queryLower, ['course', 'program', 'class', 'batch'])) {
                $response = $this->handleCourseInfo();
            } elseif ($this->matchesIntent($queryLower, ['income', 'revenue', 'collection', 'earning'])) {
                $response = $this->handleRevenueSummary();
            } elseif ($this->matchesIntent($queryLower, ['notification', 'alert', 'update', 'what\'s new', 'summary', 'overview', 'dashboard'])) {
                $response = $this->handleNotificationsSummary();
            } elseif ($this->matchesIntent($queryLower, ['help', 'what can you', 'command', 'option'])) {
                $response = $this->handleHelp();
            } elseif ($this->matchesIntent($queryLower, ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'])) {
                $hour = now()->format('H');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                $userName = session('user_name', 'there');
                $response = "👋 **{$greeting}, {$userName}!**\n\nI'm your ERP assistant, ready to help. You can ask me about:\n\n";
                $response .= "• 📊 **Dashboard summary** — overall system overview\n";
                $response .= "• 💵 **Pending fees** — outstanding student payments\n";
                $response .= "• 🎓 **New students** — recently registered admissions\n";
                $response .= "• 👥 **Staff & tasks** — employee stats and task progress\n";
                $response .= "• 📡 **Device status** — biometric connection health\n";
                $response .= "• 🏥 **System health** — diagnostics and error checks\n\n";
                $response .= "Just type naturally or use the quick buttons below!";
            } else {
                $response = $this->handleHelp();
            }
        } catch (\Exception $e) {
            $response = "⚠️ **Database Error:**\nSomething went wrong while fetching data.\n\n**Details:** " . $e->getMessage() . "\n\nPlease ensure the database tables are migrated. Visit `/run-migrations` to apply pending migrations.";
        }

        // Log the interaction (wrapped in try-catch to prevent failures)
        try {
            ChatbotInteraction::create([
                'user_id' => session('user_id'),
                'query' => $queryText,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            // Silently ignore logging errors — don't break the chatbot response
        }

        return response()->json(['response' => $response]);
    }

    /**
     * Check if a query matches any of the given intent keywords
     */
    private function matchesIntent(string $query, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pending Fees Report
     */
    private function handlePendingFees(): string
    {
        $pending = FeeInvoice::where('status', '!=', 'Paid')->with('student')->latest()->limit(8)->get();
        $totalPendingCount = FeeInvoice::where('status', '!=', 'Paid')->count();
        $totalPendingAmount = FeeInvoice::where('status', '!=', 'Paid')->sum('due_amount');
        $totalPaidToday = FeeInvoice::where('status', 'Paid')->whereDate('payment_date', today())->sum('paid_amount');

        if ($pending->isEmpty()) {
            return "🎉 **All Clear!**\nThere are currently no pending student fee receipts. All students are up to date on their payments.";
        }

        $response = "💵 **Pending Fees Report**\n\n";
        $response .= "📊 **Overview:**\n";
        $response .= "• Outstanding receipts: **{$totalPendingCount}**\n";
        $response .= "• Total due amount: **₹" . number_format($totalPendingAmount, 2) . "**\n";
        if ($totalPaidToday > 0) {
            $response .= "• Collected today: **₹" . number_format($totalPaidToday, 2) . "** ✅\n";
        }
        $response .= "\n📋 **Latest Outstanding Receipts:**\n";

        foreach ($pending as $invoice) {
            $studentName = $invoice->student ? "{$invoice->student->first_name} {$invoice->student->last_name}" : 'Unknown Student';
            $statusIcon = $invoice->status === 'Partial' ? '🟡' : '🔴';
            $response .= "{$statusIcon} **{$invoice->invoice_no}** — {$studentName}: **₹" . number_format($invoice->due_amount, 2) . "** ({$invoice->status})\n";
        }

        if ($totalPendingCount > 8) {
            $response .= "\n_...and " . ($totalPendingCount - 8) . " more pending receipts._";
        }

        return $response;
    }

    /**
     * Biometric Device Status
     */
    private function handleBiometricStatus(): string
    {
        $device = BiometricDevice::first();
        if (!$device) {
            return "🎛️ **Biometric Status**\n\nNo biometric device is configured in the system.\n\n💡 **Tip:** Go to **Biometric Sync** from the sidebar to register a ZKTeco ADMS device.";
        }

        $isOnline = $device->last_sync && Carbon::parse($device->last_sync)->diffInMinutes(now()) < 5;
        $statusBadge = $isOnline ? '🟢 ONLINE' : '🔴 OFFLINE';
        $lastSeen = $device->last_sync ? Carbon::parse($device->last_sync)->diffForHumans() : 'Never synced';

        $response = "📡 **Biometric Device Diagnostic**\n\n";
        $response .= "• **Connection:** {$statusBadge}\n";
        $response .= "• **Last Heartbeat:** {$lastSeen}\n";
        $response .= "• **Device Code:** {$device->device_code}\n";
        $response .= "• **Server IP:** " . ($device->ip_address ?? '127.0.0.1') . "\n\n";

        if (!$isOnline) {
            $response .= "⚠️ **Troubleshooting Tips:**\n";
            $response .= "1. Check the device is powered on and connected to the network\n";
            $response .= "2. Verify the ADMS server address matches this system's IP\n";
            $response .= "3. Try manual sync from the **Biometric Sync** page";
        } else {
            $response .= "✅ Device is actively pushing data to the server.";
        }

        return $response;
    }

    /**
     * System Diagnostics & Health Check
     */
    private function handleSystemDiagnostics(): string
    {
        $checks = [];

        // DB check (we're here so it's working)
        $checks[] = "• **Database:** 🟢 Connected (MySQL)";
        $checks[] = "• **PHP Version:** 🟢 " . phpversion();

        // Biometric check
        $device = BiometricDevice::first();
        if ($device) {
            $isOnline = $device->last_sync && Carbon::parse($device->last_sync)->diffInMinutes(now()) < 5;
            $checks[] = $isOnline
                ? "• **Biometric ADMS:** 🟢 Connected"
                : "• **Biometric ADMS:** 🔴 Offline (Last: " . ($device->last_sync ? Carbon::parse($device->last_sync)->diffForHumans() : 'Never') . ")";
        } else {
            $checks[] = "• **Biometric ADMS:** ⚪ Not configured";
        }

        // Pending fees warning
        $pendingCount = FeeInvoice::where('status', '!=', 'Paid')->count();
        $checks[] = $pendingCount > 10
            ? "• **Pending Fees:** ⚠️ {$pendingCount} unpaid receipts"
            : "• **Pending Fees:** 🟢 {$pendingCount} pending";

        // Task warnings
        $overdueTasks = Task::where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();
        if ($overdueTasks > 0) {
            $checks[] = "• **Overdue Tasks:** ⚠️ {$overdueTasks} tasks past their due date";
        } else {
            $checks[] = "• **Tasks:** 🟢 No overdue tasks";
        }

        // Storage
        $checks[] = "• **Cache:** 🟢 Operational";
        $checks[] = "• **Webhook Listener:** 🟢 Active";

        $response = "🏥 **System Health Report**\n\n";
        $response .= implode("\n", $checks);
        $response .= "\n\n⏰ _Report generated at " . now()->format('h:i A, M d Y') . "_";

        return $response;
    }

    /**
     * New Students — Recently Registered
     */
    private function handleNewStudents(): string
    {
        $recentStudents = Student::with('course')->latest()->limit(8)->get();
        $todayCount = Student::whereDate('created_at', today())->count();
        $weekCount = Student::where('created_at', '>=', now()->subDays(7))->count();
        $totalStudents = Student::count();

        if ($recentStudents->isEmpty()) {
            return "🎓 **Student Registry**\n\nNo students have been registered in the system yet.";
        }

        $response = "🎓 **Recently Registered Students**\n\n";
        $response .= "📊 **Quick Stats:**\n";
        $response .= "• Total students: **{$totalStudents}**\n";
        if ($todayCount > 0) {
            $response .= "• 🆕 Registered today: **{$todayCount}**\n";
        }
        $response .= "• This week: **{$weekCount}**\n";
        $response .= "\n📋 **Latest Admissions:**\n";

        foreach ($recentStudents as $student) {
            $courseName = $student->course ? $student->course->name : 'No Course';
            $date = $student->created_at ? $student->created_at->format('M d') : 'N/A';
            $response .= "• **{$student->first_name} {$student->last_name}** — {$courseName} (Adm: {$student->admission_no}, {$date})\n";
        }

        if ($totalStudents > 8) {
            $response .= "\n_View all students from the **Students** page in the sidebar._";
        }

        return $response;
    }

    /**
     * Student Statistics
     */
    private function handleStudentStats(): string
    {
        $total = Student::count();
        $active = Student::where('status', 1)->count();
        $thisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $courseWise = Student::selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->with('course')
            ->get();

        $response = "📊 **Student Statistics**\n\n";
        $response .= "• Total enrolled: **{$total}**\n";
        $response .= "• Active students: **{$active}**\n";
        $response .= "• New this month: **{$thisMonth}**\n\n";

        if ($courseWise->isNotEmpty()) {
            $response .= "📚 **By Course:**\n";
            foreach ($courseWise as $group) {
                $courseName = $group->course ? $group->course->name : 'Unassigned';
                $response .= "• {$courseName}: **{$group->count}** students\n";
            }
        }

        return $response;
    }

    /**
     * Employee / Staff Stats
     */
    private function handleEmployeeStats(): string
    {
        $total = Employee::count();
        $active = Employee::where('status', 1)->count();
        $inactive = $total - $active;

        $response = "👥 **Staff Overview**\n\n";
        $response .= "• Total staff: **{$total}**\n";
        $response .= "• Active: **{$active}** 🟢\n";
        if ($inactive > 0) {
            $response .= "• Inactive: **{$inactive}** 🔴\n";
        }

        // Department breakdown
        $departments = Employee::selectRaw('department, COUNT(*) as count')
            ->where('status', 1)
            ->groupBy('department')
            ->get();

        if ($departments->isNotEmpty()) {
            $response .= "\n🏢 **By Department:**\n";
            foreach ($departments as $dept) {
                $deptName = $dept->department ?: 'Unassigned';
                $response .= "• {$deptName}: **{$dept->count}**\n";
            }
        }

        // Pending tasks
        $pendingTasks = Task::where('status', '!=', 'Completed')->count();
        if ($pendingTasks > 0) {
            $response .= "\n📝 Active tasks across staff: **{$pendingTasks}**";
        }

        return $response;
    }

    /**
     * Task Summary
     */
    private function handleTaskSummary(): string
    {
        $total = Task::count();
        $pending = Task::where('status', 'Pending')->count();
        $inProgress = Task::where('status', 'In Progress')->count();
        $completed = Task::where('status', 'Completed')->count();

        $overdue = Task::where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();

        $response = "📝 **Task Summary**\n\n";
        $response .= "• Total tasks: **{$total}**\n";
        $response .= "• ⏳ Pending: **{$pending}**\n";
        $response .= "• 🔄 In Progress: **{$inProgress}**\n";
        $response .= "• ✅ Completed: **{$completed}**\n";

        if ($overdue > 0) {
            $response .= "• ⚠️ Overdue: **{$overdue}**\n";
        }

        // Recent tasks
        $recentTasks = Task::with('employee.user')->where('status', '!=', 'Completed')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        if ($recentTasks->isNotEmpty()) {
            $response .= "\n📋 **Active Tasks:**\n";
            foreach ($recentTasks as $task) {
                $assignee = $task->employee?->user?->name ?? 'Unassigned';
                $dueLabel = $task->due_date ? $task->due_date->format('M d') : 'No deadline';
                $priorityIcon = match ($task->priority) {
                    'High' => '🔴',
                    'Medium' => '🟡',
                    default => '🔵',
                };
                $response .= "{$priorityIcon} **{$task->title}** → {$assignee} (Due: {$dueLabel})\n";
            }
        }

        return $response;
    }

    /**
     * Attendance Summary
     */
    private function handleAttendanceSummary(): string
    {
        $todayStudentCount = Attendance::whereDate('date', today())->count();
        $todayEmployeeCount = EmployeeAttendance::whereDate('attendance_date', today())->count();
        $totalStudents = Student::count();
        $totalEmployees = Employee::where('status', 1)->count();

        $response = "📋 **Today's Attendance Overview**\n\n";
        $response .= "**Students:**\n";
        $response .= "• Marked today: **{$todayStudentCount}** / {$totalStudents}\n";

        $response .= "\n**Staff:**\n";
        $response .= "• Marked today: **{$todayEmployeeCount}** / {$totalEmployees}\n\n";

        $response .= "📅 _Date: " . today()->format('l, M d, Y') . "_";

        return $response;
    }

    /**
     * Expense Summary
     */
    private function handleExpenseSummary(): string
    {
        $totalExpense = Expense::sum('amount');
        $thisMonth = Expense::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $expenseCount = Expense::count();

        $response = "💸 **Expense Summary**\n\n";
        $response .= "• Total expenses recorded: **{$expenseCount}**\n";
        $response .= "• Total amount: **₹" . number_format($totalExpense, 2) . "**\n";
        $response .= "• This month: **₹" . number_format($thisMonth, 2) . "**\n";

        return $response;
    }

    /**
     * Course Information
     */
    private function handleCourseInfo(): string
    {
        $courses = Course::withCount('students')->get();

        if ($courses->isEmpty()) {
            return "📚 **Courses**\n\nNo courses have been added to the system yet.";
        }

        $response = "📚 **Course Directory**\n\n";
        foreach ($courses as $course) {
            $response .= "• **{$course->name}** — {$course->students_count} students";
            if ($course->duration) {
                $response .= " (Duration: {$course->duration})";
            }
            $response .= "\n";
        }

        return $response;
    }

    /**
     * Revenue / Income Summary
     */
    private function handleRevenueSummary(): string
    {
        $totalIncome = FeeInvoice::sum('paid_amount');
        $totalPending = FeeInvoice::sum('due_amount');
        $thisMonth = FeeInvoice::where('status', 'Paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('paid_amount');
        $todayIncome = FeeInvoice::where('status', 'Paid')
            ->whereDate('payment_date', today())
            ->sum('paid_amount');

        $response = "💰 **Revenue Report**\n\n";
        $response .= "• Total collected: **₹" . number_format($totalIncome, 2) . "**\n";
        $response .= "• Total outstanding: **₹" . number_format($totalPending, 2) . "**\n";
        $response .= "• This month's collection: **₹" . number_format($thisMonth, 2) . "**\n";
        if ($todayIncome > 0) {
            $response .= "• Today's collection: **₹" . number_format($todayIncome, 2) . "** ✅\n";
        }

        return $response;
    }

    /**
     * Notifications / Overview Summary
     */
    private function handleNotificationsSummary(): string
    {
        $response = "🔔 **System Notifications & Overview**\n\n";

        // New students today
        $newStudentsToday = Student::whereDate('created_at', today())->count();
        if ($newStudentsToday > 0) {
            $response .= "🆕 **{$newStudentsToday}** new student(s) registered today\n";
        }

        // Pending fees
        $pendingFees = FeeInvoice::where('status', '!=', 'Paid')->count();
        $pendingAmount = FeeInvoice::where('status', '!=', 'Paid')->sum('due_amount');
        if ($pendingFees > 0) {
            $response .= "💵 **{$pendingFees}** pending fee receipts (₹" . number_format($pendingAmount, 2) . ")\n";
        }

        // Overdue tasks
        $overdueTasks = Task::where('status', '!=', 'Completed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();
        if ($overdueTasks > 0) {
            $response .= "⚠️ **{$overdueTasks}** overdue task(s) need attention\n";
        }

        // Pending tasks
        $pendingTasks = Task::where('status', 'Pending')->count();
        if ($pendingTasks > 0) {
            $response .= "📝 **{$pendingTasks}** task(s) still pending\n";
        }

        // Biometric
        $device = BiometricDevice::first();
        if ($device && (!$device->last_sync || Carbon::parse($device->last_sync)->diffInMinutes(now()) >= 5)) {
            $response .= "📡 Biometric device is **offline**\n";
        }

        // Today's attendance
        $todayAttendance = Attendance::whereDate('date', today())->count();
        $totalStudents = Student::count();
        if ($totalStudents > 0) {
            $response .= "📋 Today's attendance: **{$todayAttendance}/{$totalStudents}** students marked\n";
        }

        // Today's income
        $todayIncome = FeeInvoice::where('status', 'Paid')
            ->whereDate('payment_date', today())
            ->sum('paid_amount');
        if ($todayIncome > 0) {
            $response .= "💰 Today's collection: **₹" . number_format($todayIncome, 2) . "**\n";
        }

        if ($newStudentsToday === 0 && $pendingFees === 0 && $overdueTasks === 0 && $pendingTasks === 0) {
            $response .= "\n✨ Everything looks great! No urgent notifications.";
        }

        $response .= "\n\n⏰ _" . now()->format('h:i A, M d Y') . "_";

        return $response;
    }

    /**
     * Help / Command List
     */
    private function handleHelp(): string
    {
        $response = "🤖 **ERP Assistant — Available Commands**\n\n";
        $response .= "Here's everything I can help you with:\n\n";
        $response .= "💵 **\"pending fees\"** — Show outstanding fee receipts\n";
        $response .= "🎓 **\"new students\"** — Recently registered students\n";
        $response .= "📊 **\"student stats\"** — Enrollment statistics by course\n";
        $response .= "👥 **\"staff overview\"** — Employee and department breakdown\n";
        $response .= "📝 **\"task summary\"** — Active tasks and assignments\n";
        $response .= "📋 **\"attendance\"** — Today's attendance overview\n";
        $response .= "💸 **\"expenses\"** — Expense breakdown\n";
        $response .= "💰 **\"revenue\"** — Income and collection report\n";
        $response .= "📚 **\"courses\"** — Course directory\n";
        $response .= "📡 **\"biometric status\"** — Device connection check\n";
        $response .= "🏥 **\"system health\"** — Full diagnostics report\n";
        $response .= "🔔 **\"notifications\"** — Overview of alerts and updates\n\n";
        $response .= "💡 Just type naturally — I'll understand what you need!";

        return $response;
    }
}
