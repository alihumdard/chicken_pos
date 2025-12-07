<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        // Fetch expenses, latest first
        $expenses = Expense::orderBy('date', 'desc')->get();
        return view('pages.expense.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:1',
            'category'    => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            Expense::create($validated);
            return response()->json(['success' => true, 'message' => 'Expense added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        if ($expense) {
            return response()->json(['success' => true, 'data' => $expense]);
        }
        return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:1',
            'category'    => 'required|string',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $expense = Expense::findOrFail($id);
            $expense->update($validated);
            return response()->json(['success' => true, 'message' => 'Expense updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Expense::destroy($id);
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}