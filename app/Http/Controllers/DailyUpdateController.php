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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No staff profile found.'], 404);
            }
            return back()->withErrors(['error' => 'No staff profile found.']);
        }

        $validationRules = [
            'work_title' => ['nullable', 'string', 'max:255'],
            'update_text' => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'file', 'max:5120'], // Max 5MB
        ];

        if ($request->ajax() || $request->wantsJson()) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $validationRules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();
        } else {
            $validatedData = $request->validate($validationRules);
        }

        $data = [
            'employee_id' => $employee->id,
            'work_title' => $validatedData['work_title'] ?? null,
            'update_text' => $validatedData['update_text'],
            'date' => now()->toDateString(),
        ];

        // Handle attachment file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/daily_updates'), $filename);
            $data['file_path'] = 'uploads/daily_updates/' . $filename;
        }

        // Check if already logged today
        $existing = DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', $data['date'])
            ->first();

        if ($existing) {
            // Delete old file if new one is uploaded
            if (isset($data['file_path']) && $existing->file_path && file_exists(public_path($existing->file_path))) {
                @unlink(public_path($existing->file_path));
            }
            
            $existing->update([
                'work_title' => $data['work_title'] ?? $existing->work_title,
                'update_text' => $data['update_text'],
                'file_path' => $data['file_path'] ?? $existing->file_path,
            ]);

            $msg = 'Your daily update log for today has been updated successfully!';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data' => $existing
                ]);
            }
            return back()->with('success', $msg);
        }

        $newLog = DailyUpdate::create($data);

        $msg = 'Your daily update log has been submitted successfully!';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'data' => $newLog
            ]);
        }
        return back()->with('success', $msg);
    }
}
