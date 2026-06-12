<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalarySlip;
use Illuminate\Http\Request;

class SalarySlipController extends Controller
{
    public function index(Request $request)
    {
        $query = SalarySlip::with('employee');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->whereHas('employee.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('employee', function ($q) use ($request) {
                $q->where('employee_code', 'like', '%' . $request->search . '%');
            });
        }

        $salarySlips = $query->latest()->paginate(12)->withQueryString();

        return view('salary_slips.index', compact('salarySlips'));
    }

    public function calculateDeduction(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'month' => ['required', 'string'],
            'year' => ['required', 'digits:4'],
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        $monthStr = $request->month;
        $year = $request->year;
        $monthNum = date('m', strtotime($monthStr . ' 1 ' . $year));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

        // Calculate present days
        $attendances = \App\Models\EmployeeAttendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $monthNum)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Present')
            ->count();

        $absentDays = max(0, $daysInMonth - $attendances);
        
        $basicSalary = $employee->salary ?? 0;
        $perDaySalary = $basicSalary / $daysInMonth;
        
        $deduction = round($absentDays * $perDaySalary, 2);
        
        return response()->json([
            'basic_salary' => $basicSalary,
            'deduction' => $deduction,
            'attended_days' => $attendances,
            'total_days' => $daysInMonth,
            'absent_days' => $absentDays,
        ]);
    }

    public function create()
    {
        return view('salary_slips.create', [
            'employees' => Employee::with('user')->orderBy('employee_code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'month' => ['required', 'string', 'max:20'],
            'year' => ['required', 'digits:4'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', 'in:Pending,Paid'],
        ]);

        $data['allowances'] = $data['allowances'] ?? 0;
        $data['deductions'] = $data['deductions'] ?? 0;
        $data['net_pay'] = max(0, $data['basic_salary'] + $data['allowances'] - $data['deductions']);
        $data['created_by'] = session('user_id');

        SalarySlip::create($data);

        return redirect()->route('salary_slips.index')->with('success', 'Salary slip generated successfully.');
    }

    public function destroy(SalarySlip $salarySlip)
    {
        $salarySlip->delete();

        return back()->with('success', 'Salary slip deleted successfully.');
    }

    public function show(SalarySlip $salarySlip)
    {
        $salarySlip->load('employee.user', 'creator');
        return view('salary_slips.show', compact('salarySlip'));
    }

    public function restore($id)
    {
        $salarySlip = SalarySlip::onlyTrashed()->findOrFail($id);
        $salarySlip->restore();

        return back()->with('success', 'Salary slip restored successfully.');
    }
}
