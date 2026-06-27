<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Parse dates
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        // Build query for lists
        $query = FeeInvoice::with(['student.course']);

        if ($fromDate) {
            $query->where('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('payment_date', '<=', $toDate);
        }
        if ($request->filled('course_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('fee_category')) {
            $query->where('fee_category', 'like', '%'.$request->fee_category.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get matching invoices
        $invoices = $query->latest('payment_date')->paginate(15)->withQueryString();

        // Calculate Summaries based on filtered query (for accuracy)
        $summaryQuery = FeeInvoice::query();
        if ($fromDate) {
            $summaryQuery->where('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $summaryQuery->where('payment_date', '<=', $toDate);
        }
        if ($request->filled('course_id')) {
            $summaryQuery->whereHas('student', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('payment_method')) {
            $summaryQuery->where('payment_method', $request->payment_method);
        }
        if ($request->filled('fee_category')) {
            $summaryQuery->where('fee_category', 'like', '%'.$request->fee_category.'%');
        }
        if ($request->filled('status')) {
            $summaryQuery->where('status', $request->status);
        }

        $totalCollected = $summaryQuery->sum('paid_amount');
        $totalDiscount = $summaryQuery->sum('discount');
        $totalFine = $summaryQuery->sum('fine');
        $totalDue = $summaryQuery->sum('due_amount');
        $totalInvoicesCount = $summaryQuery->count();

        // Extra info for dropdowns
        $courses = Course::orderBy('name')->get();
        $paymentMethods = ['Cash', 'Online', 'Cheque', 'UPI'];
        $feeCategories = ['Admission Fee', 'Registration Fee', 'Prospectus Fee', 'Monthly Course Fee', 'Seminar', 'Fine', 'Other'];

        return view('reports.index', compact(
            'invoices',
            'totalCollected',
            'totalDiscount',
            'totalFine',
            'totalDue',
            'totalInvoicesCount',
            'courses',
            'paymentMethods',
            'feeCategories'
        ));
    }

    public function exportCsv(Request $request)
    {
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        $query = FeeInvoice::with(['student.course']);

        if ($fromDate) {
            $query->where('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('payment_date', '<=', $toDate);
        }
        if ($request->filled('course_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('fee_category')) {
            $query->where('fee_category', 'like', '%'.$request->fee_category.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('payment_date')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=fee_report_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Receipt No', 'Student Name', 'Admission No', 'Course', 'Category', 'Total (INR)', 'Paid (INR)', 'Discount (INR)', 'Fine (INR)', 'Due (INR)', 'Payment Date', 'Method', 'Status']);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->student?->first_name . ' ' . $invoice->student?->last_name,
                    $invoice->student?->admission_no ?? '-',
                    $invoice->student?->course?->name ?? '-',
                    $invoice->fee_category ?? 'Fees',
                    $invoice->total_amount,
                    $invoice->paid_amount,
                    $invoice->discount,
                    $invoice->fine,
                    $invoice->due_amount,
                    $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : '-',
                    $invoice->payment_method ?? '-',
                    $invoice->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        $query = FeeInvoice::with(['student.course']);

        if ($fromDate) {
            $query->where('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('payment_date', '<=', $toDate);
        }
        if ($request->filled('course_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('fee_category')) {
            $query->where('fee_category', 'like', '%'.$request->fee_category.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('payment_date')->get();

        $totalCollected = $invoices->sum('paid_amount');
        $totalDiscount = $invoices->sum('discount');
        $totalFine = $invoices->sum('fine');
        $totalDue = $invoices->sum('due_amount');

        return view('reports.print', compact(
            'invoices',
            'totalCollected',
            'totalDiscount',
            'totalFine',
            'totalDue',
            'fromDate',
            'toDate'
        ));
    }
}
