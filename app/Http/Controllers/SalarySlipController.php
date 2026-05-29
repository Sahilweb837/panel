<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalarySlip;
use Illuminate\Http\Request;

class SalarySlipController extends Controller
{
    public function index(Request $request)
    {
        $salarySlips = SalarySlip::with('employee')->latest()->paginate(12);

        return view('salary_slips.index', compact('salarySlips'));
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
}
