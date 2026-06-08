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
            'attendance.*.check_out_time' => ['nullable', 'string'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
            'attendance.*.photo' => ['nullable', 'string'],
        ]);

        $date = $request->attendance_date;

        foreach ($request->attendance as $employeeId => $data) {
            $photoPath = null;
            if (isset($data['photo']) && !empty($data['photo'])) {
                $image_parts = explode(";base64,", $data['photo']);
                if (count($image_parts) == 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1] ?? 'jpeg';
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = 'attendance_faces/staff_' . $employeeId . '_' . uniqid() . '.' . $image_type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                    $photoPath = 'storage/' . $fileName;
                }
            }

            $updateData = [
                'status' => $data['status'],
                'check_in_time' => $data['check_in_time'] ?? null,
                'check_out_time' => $data['check_out_time'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ];

            if ($photoPath) {
                $updateData['photo_path'] = $photoPath;
            }

            EmployeeAttendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $date,
                ],
                $updateData
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

    public function exportCsv(Request $request)
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

        $attendances = $query->latest('attendance_date')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=staff_attendance_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Employee Code', 'Staff Name', 'Attendance Date', 'Status', 'Check-in Time', 'Check-out Time', 'Remarks']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->employee?->employee_code ?? '-',
                    $attendance->employee?->user?->name ?? '-',
                    $attendance->attendance_date,
                    $attendance->status,
                    $attendance->check_in_time ?? '-',
                    $attendance->check_out_time ?? '-',
                    $attendance->remarks ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
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

        $attendances = $query->latest('attendance_date')->get();

        return view('employee_attendances.print', compact('attendances'));
    }
}
