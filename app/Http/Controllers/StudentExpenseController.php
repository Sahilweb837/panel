<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class StudentExpenseController extends Controller
{
    /**
     * Display a summary of expenses per month.
     */
    public function index()
    {
        // Group expenses by month (YYYY-MM) and sum the amount
        $monthlyExpenses = Expense::selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('student_expenses.index', compact('monthlyExpenses'));
    }
}
