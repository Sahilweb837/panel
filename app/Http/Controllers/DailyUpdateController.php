<?php

namespace App\Http\Controllers;

use App\Models\DailyUpdate;
use App\Models\Employee;
use Illuminate\Http\Request;

class DailyUpdateController extends Controller
{
    public function index(Request $request)
    {
        // Only Super Admin can view all daily updates
        if (session('user_role_slug') === 'staff') {
            return redirect()->route('staff.dashboard')->withErrors(['error' => 'Unauthorized access.']);
        }

        $query = DailyUpdate::with('employee.user');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $updates = $query->latest('date')->latest('created_at')->paginate(20)->withQueryString();
        $employees = Employee::with('user')->where('status', 1)->get();

        return view('daily_updates.index', compact('updates', 'employees'));
    }

    public function store(Request $request)
    {
        $employee = Employee::where('user_id', session('user_id'))->first();
        if (!$employee) {
            return back()->withErrors(['error' => 'No staff profile found.']);
        }

        $data = $request->validate([
            'update_text' => ['required', 'string', 'min:10'],
        ]);

        $data['employee_id'] = $employee->id;
        $data['date'] = now()->toDateString();

        // Check if already logged today
        $existing = DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', $data['date'])
            ->first();

        if ($existing) {
            $existing->update_text = $data['update_text'];
            $existing->save();
            return back()->with('success', 'Your daily update log for today has been updated successfully!');
        }

        DailyUpdate::create($data);

        return back()->with('success', 'Your daily update log has been submitted successfully!');
    }
}
