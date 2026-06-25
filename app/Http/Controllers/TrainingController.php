<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Course;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Training::with(['course', 'creator']);

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('slip_no', 'like', '%'.$request->search.'%')
                ->orWhere('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('mobile', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        $trainings = $query->latest()->paginate(12)->withQueryString();
        $courses = Course::orderBy('name')->get();
        $durations = ['28 Days', '45 Days', '1 Month', '3 Months', '6 Months'];

        return view('trainings.index', compact('trainings', 'courses', 'durations'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get();
        $durations = ['28 Days', '45 Days', '1 Month', '3 Months', '6 Months'];

        return view('trainings.create', compact('courses', 'durations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'college' => ['nullable', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:20'],
            'course_id' => ['required', 'exists:courses,id'],
            'duration' => ['required', 'string', 'max:50'],
            'fees' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:Cash,Online,Cheque,UPI'],
            'upi_transaction_id' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'status' => ['required', 'in:Paid,Unpaid'],
        ]);

        $slipNo = 'TR-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -4));

        $data['slip_no'] = $slipNo;
        $data['created_by'] = session('user_id');

        Training::create($data);

        return redirect()->route('trainings.index')->with('success', 'Training slip generated successfully.');
    }

    public function show(Training $training)
    {
        $training->load('course', 'creator');

        return view('trainings.show', compact('training'));
    }

    public function destroy(Training $training)
    {
        $training->delete();

        return back()->with('success', 'Training slip deleted successfully.');
    }

    public function restore($id)
    {
        $training = Training::onlyTrashed()->findOrFail($id);
        $training->restore();

        return back()->with('success', 'Training slip restored successfully.');
    }

    public function exportCsv(Request $request)
    {
        $query = Training::with(['course', 'creator']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        $trainings = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="trainings_'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function() use ($trainings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Slip No', 'Name', 'Father Name', 'Email', 'College', 'Mobile', 'Course', 'Duration', 'Fees', 'Payment Method', 'UPI Transaction ID', 'Payment Date', 'Status', 'Created By', 'Created At']);

            foreach ($trainings as $training) {
                fputcsv($file, [
                    $training->slip_no,
                    $training->name,
                    $training->father_name,
                    $training->email,
                    $training->college,
                    $training->mobile,
                    $training->course->name ?? 'N/A',
                    $training->duration,
                    $training->fees,
                    $training->payment_method,
                    $training->upi_transaction_id,
                    $training->payment_date ? $training->payment_date->format('Y-m-d') : '',
                    $training->status,
                    $training->creator->name ?? 'N/A',
                    $training->created_at ? $training->created_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function analytics(Request $request)
    {
        $totalRegistrations = Training::count();
        $totalRevenue = Training::sum('fees');

        $courseStats = Training::selectRaw('course_id, COUNT(*) as count, SUM(fees) as revenue')
            ->with('course')
            ->groupBy('course_id')
            ->get();

        $durationStats = Training::selectRaw('duration, COUNT(*) as count')
            ->groupBy('duration')
            ->get();

        $recentRegistrations = Training::with(['course', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $filteredQuery = clone Training::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $filteredQuery->whereBetween('payment_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $filteredQuery->where('payment_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $filteredQuery->where('payment_date', '<=', $request->to_date);
        }

        if ($request->filled('course_id')) {
            $filteredQuery->where('course_id', $request->course_id);
        }

        $filteredRegistrations = $filteredQuery->count();
        $filteredRevenue = $filteredQuery->sum('fees');

        return view('trainings.analytics', compact(
            'totalRegistrations',
            'totalRevenue',
            'courseStats',
            'durationStats',
            'recentRegistrations',
            'filteredRegistrations',
            'filteredRevenue'
        ));
    }
}
