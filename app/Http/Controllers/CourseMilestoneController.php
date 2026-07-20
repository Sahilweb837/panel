<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMilestone;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourseMilestoneController extends Controller
{
    public function index($courseId)
    {
        $course = Course::with('milestones')->findOrFail($courseId);
        $milestones = $course->milestones;

        $totalCount = $milestones->count();
        $completedCount = $milestones->where('is_completed', true)->count();
        $progressPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        return view('courses.milestones', compact('course', 'milestones', 'totalCount', 'completedCount', 'progressPercentage'));
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $request->validate([
            'milestone_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer',
        ]);

        $maxOrder = $course->milestones()->max('order_index') ?? 0;

        CourseMilestone::create([
            'course_id' => $course->id,
            'milestone_title' => $request->milestone_title,
            'description' => $request->description,
            'order_index' => $request->filled('order_index') ? $request->order_index : ($maxOrder + 1),
            'is_completed' => false,
        ]);

        return redirect()->route('courses.milestones.index', $course->id)->with('success', 'Syllabus milestone added successfully.');
    }

    public function toggleCompletion(Request $request, $id)
    {
        $milestone = CourseMilestone::findOrFail($id);
        $milestone->is_completed = !$milestone->is_completed;

        if ($milestone->is_completed) {
            $milestone->completed_at = Carbon::now();
            $milestone->covered_by = session('user_name', 'Trainer');
        } else {
            $milestone->completed_at = null;
            $milestone->covered_by = null;
        }

        $milestone->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_completed' => $milestone->is_completed,
                'completed_at' => $milestone->completed_at ? $milestone->completed_at->format('M d, Y h:i A') : null,
                'covered_by' => $milestone->covered_by,
            ]);
        }

        return back()->with('success', 'Milestone status updated successfully.');
    }

    public function destroy($id)
    {
        $milestone = CourseMilestone::findOrFail($id);
        $courseId = $milestone->course_id;
        $milestone->delete();

        return redirect()->route('courses.milestones.index', $courseId)->with('success', 'Milestone topic removed successfully.');
    }
}
