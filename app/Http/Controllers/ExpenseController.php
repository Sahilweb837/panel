<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('category', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('date')) {
            $query->whereDate('expense_date', $request->date);
        }

        $expenses = $query->latest()->paginate(12)->withQueryString();

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'expense_date' => ['required', 'date'],
        ]);

        Expense::create(array_merge($data, [
            'created_by' => session('user_id'),
        ]));

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    public function restore($id)
    {
        $expense = Expense::onlyTrashed()->findOrFail($id);
        $expense->restore();

        return back()->with('success', 'Expense restored successfully.');
    }
}
