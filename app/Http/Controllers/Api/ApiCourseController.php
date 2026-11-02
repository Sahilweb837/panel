<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiCourseController extends Controller
{
    /**
     * Display a listing of courses formatted for both panel ERP and frontend LMS sync.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (bool)$request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $courses = $query->withCount('students')
                         ->orderBy('name', 'asc')
                         ->get();

        $formatted = $courses->map(function ($c) {
            $weeks = max(1, (int)filter_var($c->duration, FILTER_SANITIZE_NUMBER_INT) ?: 8);
            return [
                'id'            => $c->id,
                'name'          => $c->name,
                'title'         => $c->name,
                'slug'          => Str::slug($c->name),
                'code'          => $c->code,
                'duration'      => $c->duration,
                'durationWeeks' => $weeks,
                'fee'           => (float)$c->fee,
                'price'         => (float)$c->fee,
                'originalPrice' => (float)$c->fee,
                'currency'      => 'INR',
                'category'      => 'Development',
                'categorySlug'  => 'development',
                'status'        => (bool)$c->status,
                'students_count'=> $c->students_count ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $formatted->count(),
            'data'    => $formatted
        ]);
    }

    /**
     * Display online courses for frontend LMS sync.
     */
    public function onlineCourses(Request $request)
    {
        $trainingCourses = TrainingCourse::where('status', true)->get();

        $data = $trainingCourses->map(function ($tc) {
            return [
                'id'                   => $tc->id,
                'title'                => $tc->name,
                'slug'                 => Str::slug($tc->name),
                'programming_language' => 'Full Stack & IT',
                'category'             => 'Development',
                'description'          => 'Comprehensive industrial training curriculum for '.$tc->name,
                'price'                => (float)($tc->fee ?? 0),
                'original_price'       => (float)($tc->fee ?? 0),
                'is_free'              => (float)($tc->fee ?? 0) <= 0,
                'duration'             => $tc->duration ?? '3 Months',
                'lessons_count'        => 24,
                'level'                => 'all_levels',
                'has_live_option'      => true,
                'status'               => 'published',
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $data->count(),
            'data'    => $data
        ]);
    }

    /**
     * Display the specified course with student count.
     */
    public function show($id)
    {
        $course = Course::withCount('students')->find($id);

        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $course
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:150|unique:courses,name',
            'code'     => 'required|string|max:50|unique:courses,code',
            'duration' => 'required|string|max:50',
            'fee'      => 'required|numeric|min:0',
            'status'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course = Course::create([
            'name'     => trim($request->name),
            'code'     => strtoupper(trim($request->code)),
            'duration' => trim($request->duration),
            'fee'      => (float)$request->fee,
            'status'   => $request->input('status', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully.',
            'data'    => $course
        ], 201);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:150|unique:courses,name,'.$course->id,
            'code'     => 'sometimes|required|string|max:50|unique:courses,code,'.$course->id,
            'duration' => 'sometimes|required|string|max:50',
            'fee'      => 'sometimes|required|numeric|min:0',
            'status'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $course->update($request->only(['name', 'code', 'duration', 'fee', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully.',
            'data'    => $course
        ]);
    }

    /**
     * Remove the specified course (soft delete).
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        if ($course->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete course with active students enrolled. Deactivate instead.'
            ], 400);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course archived successfully.'
        ]);
    }
}
