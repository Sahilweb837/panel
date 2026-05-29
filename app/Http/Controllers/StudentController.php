<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%')
                    ->orWhereHas('course', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('code', 'like', '%'.$request->search.'%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->paginate(12)->withQueryString();
        $courses = Course::where('status', true)->orderBy('name')->get();
        $durations = $this->courseDurations();

        return view('students.index', compact('students', 'courses', 'durations'));
    }

    public function create()
    {
        return view('students.create', [
            'courses' => Course::where('status', true)->orderBy('name')->get(),
            'durations' => $this->courseDurations(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'aadhar_number' => ['nullable', 'digits:12'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_duration' => ['nullable', 'in:45 Days,1 Month,6 Months,1 Year'],
            'student_type' => ['required', 'in:Regular (On Campus),Regular (Internship),Online'],
            'class' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status');

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        return view('students.edit', [
            'student' => $student,
            'courses' => Course::where('status', true)->orderBy('name')->get(),
            'durations' => $this->courseDurations(),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no,'.$student->id],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'aadhar_number' => ['nullable', 'digits:12'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:students,email,'.$student->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_duration' => ['nullable', 'in:45 Days,1 Month,6 Months,1 Year'],
            'student_type' => ['required', 'in:Regular (On Campus),Regular (Internship),Online'],
            'class' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status');

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Student deleted successfully.');
    }

    public function exportCsv(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=students_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Admission No', 'Roll No', 'Full Name', 'Email', 'Phone', 'Course', 'Duration', 'Type', 'Status', 'Admission Date']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_no,
                    $student->roll_no ?? '-',
                    $student->first_name . ' ' . $student->last_name,
                    $student->email ?? '-',
                    $student->phone ?? '-',
                    $student->course?->name ?? '-',
                    $student->course_duration ?? '-',
                    $student->student_type ?? '-',
                    $student->status ? 'Active' : 'Inactive',
                    $student->admission_date ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->get();

        return view('students.print', compact('students'));
    }

    private function courseDurations(): array
    {
        return ['45 Days', '1 Month', '6 Months', '1 Year'];
    }
}
