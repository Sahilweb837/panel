<?php

namespace App\Http\Controllers;

use App\Models\CourseBundle;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseBundleController extends Controller
{
    /**
     * Display Course Bundle management interface (Edaca style).
     */
    public function index(Request $request)
    {
        $query = CourseBundle::query();

        // 1. Search by title or included courses
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('courses', 'like', "%{$s}%");
            });
        }

        // 2. Filter by status
        if ($request->filled('status') && in_array($request->status, ['Active', 'Upcoming', 'Paused'])) {
            $query->where('status', $request->status);
        }

        // 3. Sort by
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');

        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder === 'asc' ? 'asc' : 'desc');
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', $sortOrder === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $bundles = $query->paginate(10)->withQueryString();
        $totalBundles = CourseBundle::count();
        $availableCourses = Course::where('status', true)->pluck('name');

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'count'   => $bundles->total(),
                'bundles' => $bundles,
            ]);
        }

        return view('lms.course_bundles', compact('bundles', 'totalBundles', 'availableCourses'));
    }

    /**
     * Store a newly created course bundle.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'courses'            => 'nullable',
            'subscription_limit' => 'required|string|max:100',
            'price'              => 'required|numeric|min:0',
            'status'             => 'required|in:Active,Upcoming,Paused',
            'thumbnail'          => 'nullable|string',
        ]);

        $coursesList = [];
        if (!empty($request->courses)) {
            if (is_array($request->courses)) {
                $coursesList = array_values(array_filter($request->courses));
            } else {
                $coursesList = array_map('trim', explode("\n", str_replace("\r", "", $request->courses)));
                $coursesList = array_values(array_filter($coursesList));
            }
        }

        $bundle = CourseBundle::create([
            'title'              => $validated['title'],
            'slug'               => Str::slug($validated['title']) . '-' . uniqid(),
            'thumbnail'          => $request->thumbnail ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop',
            'courses'            => $coursesList,
            'subscription_limit' => $validated['subscription_limit'],
            'price'              => (float)$validated['price'],
            'currency'           => '$',
            'status'             => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'bundle' => $bundle]);
        }

        return redirect()->route('lms.course_bundles.index')->with('success', 'Course bundle created successfully.');
    }

    /**
     * Update an existing course bundle.
     */
    public function update(Request $request, $id)
    {
        $bundle = CourseBundle::findOrFail($id);

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'courses'            => 'nullable',
            'subscription_limit' => 'required|string|max:100',
            'price'              => 'required|numeric|min:0',
            'status'             => 'required|in:Active,Upcoming,Paused',
        ]);

        $coursesList = [];
        if (!empty($request->courses)) {
            if (is_array($request->courses)) {
                $coursesList = array_values(array_filter($request->courses));
            } else {
                $coursesList = array_map('trim', explode("\n", str_replace("\r", "", $request->courses)));
                $coursesList = array_values(array_filter($coursesList));
            }
        }

        $bundle->update([
            'title'              => $validated['title'],
            'courses'            => $coursesList,
            'subscription_limit' => $validated['subscription_limit'],
            'price'              => (float)$validated['price'],
            'status'             => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'bundle' => $bundle]);
        }

        return redirect()->route('lms.course_bundles.index')->with('success', 'Course bundle updated successfully.');
    }

    /**
     * Delete a course bundle.
     */
    public function destroy(Request $request, $id)
    {
        $bundle = CourseBundle::findOrFail($id);
        $bundle->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('lms.course_bundles.index')->with('success', 'Course bundle deleted.');
    }

    /**
     * Toggle status (e.g. Pause, Activate).
     */
    public function toggleStatus(Request $request, $id)
    {
        $bundle = CourseBundle::findOrFail($id);
        $newStatus = $request->input('status', 'Active');

        if (in_array($newStatus, ['Active', 'Upcoming', 'Paused'])) {
            $bundle->status = $newStatus;
            $bundle->save();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'status' => $bundle->status]);
        }

        return redirect()->route('lms.course_bundles.index')->with('success', "Bundle status updated to {$bundle->status}.");
    }
}
