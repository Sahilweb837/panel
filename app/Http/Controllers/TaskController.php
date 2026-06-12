<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employee;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $userRole = session('user_role_slug');

        if ($userRole === 'staff') {
            // Staff only see their own tasks
            $employee = Employee::where('user_id', session('user_id'))->first();
            if (!$employee) {
                return redirect()->back()->withErrors(['error' => 'No staff profile found.']);
            }
            $tasks = Task::where('assigned_to', $employee->id)->orderBy('due_date', 'asc')->get();
            return view('tasks.index', compact('tasks'));
        }

        // Super Admin sees all tasks
        $query = Task::with(['employee.user', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $employees = Employee::with('user')->where('status', 1)->get();
        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['required', 'exists:employees,id'],
            'priority' => ['required', 'in:Low,Medium,High'],
            'due_date' => ['nullable', 'date'],
        ]);

        $data['created_by'] = session('user_id');
        $data['status'] = 'Pending';

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task assigned successfully to employee.');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        // Ensure the task belongs to the logged-in staff member
        $employee = Employee::where('user_id', session('user_id'))->first();
        if ($task->assigned_to !== $employee->id && session('user_role_slug') !== 'super-admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => ['required', 'in:Pending,In Progress,Completed']
        ]);

        $task->status = $request->status;
        $task->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'status' => $task->status]);
        }

        return back()->with('success', 'Task status updated successfully.');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
