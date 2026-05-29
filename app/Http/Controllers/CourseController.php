<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%')
                    ->orWhere('duration', 'like', '%'.$request->search.'%');
            });
        }

        $courses = $query->withCount('students')->latest()->paginate(12)->withQueryString();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('courses.index')->with('error', 'Only administrators can manage courses.');
        }

        return view('courses.create');
    }

    public function store(Request $request)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('courses.index')->with('error', 'Only administrators can manage courses.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:courses,name'],
            'code' => ['nullable', 'string', 'max:50', 'unique:courses,code'],
            'duration' => ['nullable', 'string', 'max:50'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['fee'] = $data['fee'] ?? 0;
        $data['status'] = $request->boolean('status');

        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('courses.index')->with('error', 'Only administrators can manage courses.');
        }

        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('courses.index')->with('error', 'Only administrators can manage courses.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:courses,name,' . $course->id],
            'code' => ['nullable', 'string', 'max:50', 'unique:courses,code,' . $course->id],
            'duration' => ['nullable', 'string', 'max:50'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['fee'] = $data['fee'] ?? 0;
        $data['status'] = $request->boolean('status');

        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('courses.index')->with('error', 'Only administrators can manage courses.');
        }

        $course->delete();

        return back()->with('success', 'Course deleted successfully.');
    }
}
