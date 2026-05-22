<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAttendance::with('employee.user');

        if ($request->filled('employee')) {
            $query->whereHas('employee', function ($query) use ($request) {
                $query->where('employee_code', 'like', '%'.$request->employee.'%')
                    ->orWhereHas('user', function ($uQuery) use ($request) {
                        $uQuery->where('name', 'like', '%'.$request->employee.'%');
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        $attendances = $query->latest('attendance_date')->paginate(15)->withQueryString();

        // Calculate some nice dashboard stats
        $today = Carbon::today()->toDateString();
        $totalEmployees = Employee::where('status', true)->count();
        
        $presentToday = EmployeeAttendance::whereDate('attendance_date', $today)
            ->where('status', 'Present')
            ->count();
            
        $absentToday = EmployeeAttendance::whereDate('attendance_date', $today)
            ->where('status', 'Absent')
            ->count();

        $leaveToday = EmployeeAttendance::whereDate('attendance_date', $today)
            ->where('status', 'Leave')
            ->count();

        return view('employee_attendances.index', compact(
            'attendances', 
            'totalEmployees', 
            'presentToday', 
            'absentToday', 
            'leaveToday'
        ));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $employees = Employee::with('user')->where('status', true)->orderBy('employee_code')->get();
        
        // Fetch existing attendance for this date if any
        $existingAttendances = EmployeeAttendance::whereDate('attendance_date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('employee_attendances.create', compact('employees', 'date', 'existingAttendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:Present,Absent,Late,Leave'],
            'attendance.*.check_in_time' => ['nullable', 'string'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $date = $request->attendance_date;

        foreach ($request->attendance as $employeeId => $data) {
            EmployeeAttendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $data['status'],
                    'check_in_time' => $data['check_in_time'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('employee-attendances.index')
            ->with('success', 'Staff attendance for ' . Carbon::parse($date)->format('M d, Y') . ' recorded successfully.');
    }

    public function destroy($id)
    {
        $attendance = EmployeeAttendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Staff attendance record deleted successfully.');
    }
}
