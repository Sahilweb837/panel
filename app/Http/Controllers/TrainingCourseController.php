<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use Illuminate\Http\Request;

class TrainingCourseController extends Controller
{
    public function index(Request $request)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Only administrators can manage training courses.');
        }

        $query = TrainingCourse::query();

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('short_code', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trainingCourses = $query->latest()->paginate(12)->withQueryString();
        $durations = ['28 Days', '45 Days', '1 Month', '3 Months', '6 Months'];

        return view('admin.training_courses.index', compact('trainingCourses', 'durations'));
    }

    public function create()
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $durations = ['28 Days', '45 Days', '1 Month', '3 Months', '6 Months'];

        return view('admin.training_courses.create', compact('durations'));
    }

    public function store(Request $request)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:training_courses,name'],
            'short_code' => ['nullable', 'string', 'max:50'],
            'duration' => ['required', 'string', 'max:50', 'in:28 Days,45 Days,1 Month,3 Months,6 Months'],
            'fee' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['fee'] = $data['fee'] ?? 0;
        $data['status'] = $request->boolean('status', true);

        TrainingCourse::create($data);

        return redirect()->route('training_courses.index')->with('success', 'Training course created successfully.');
    }

    public function edit(TrainingCourse $trainingCourse)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $durations = ['28 Days', '45 Days', '1 Month', '3 Months', '6 Months'];

        return view('admin.training_courses.edit', compact('trainingCourse', 'durations'));
    }

    public function update(Request $request, TrainingCourse $trainingCourse)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:training_courses,name,'.$trainingCourse->id],
            'short_code' => ['nullable', 'string', 'max:50'],
            'duration' => ['required', 'string', 'max:50', 'in:28 Days,45 Days,1 Month,3 Months,6 Months'],
            'fee' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['fee'] = $data['fee'] ?? 0;
        $data['status'] = $request->boolean('status', true);

        $trainingCourse->update($data);

        return redirect()->route('training_courses.index')->with('success', 'Training course updated successfully.');
    }

    public function destroy(TrainingCourse $trainingCourse)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $trainingCourse->delete();

        return back()->with('success', 'Training course deleted successfully.');
    }

    public function restore($id)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('training_courses.index')->with('error', 'Only administrators can manage training courses.');
        }

        $trainingCourse = TrainingCourse::onlyTrashed()->findOrFail($id);
        $trainingCourse->restore();

        return back()->with('success', 'Training course restored successfully.');
    }
}