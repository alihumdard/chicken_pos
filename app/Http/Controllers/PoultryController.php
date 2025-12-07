<?php

namespace App\Http\Controllers;

use App\Models\Poultry;
use Illuminate\Http\Request;

class PoultryController extends Controller
{
    public function index()
    {
        $poultries = Poultry::orderBy('entry_date', 'desc')->get();
        return view('pages.poultry.index', compact('poultries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date'   => 'required|date',
            'batch_no'     => 'nullable|string|max:50',
            'quantity'     => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0',
            'cost_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        try {
            Poultry::create($validated);
            return response()->json(['success' => true, 'message' => 'Added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 🟢 FETCH DATA FOR EDIT MODAL
    public function edit($id)
    {
        $poultry = Poultry::find($id);
        if ($poultry) {
            return response()->json(['success' => true, 'data' => $poultry]);
        }
        return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    }

    // 🟢 UPDATE DATA
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'entry_date'   => 'required|date',
            'batch_no'     => 'nullable|string|max:50',
            'quantity'     => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0',
            'cost_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        try {
            $poultry = Poultry::findOrFail($id);
            $poultry->update($validated);

            return response()->json(['success' => true, 'message' => 'Updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Poultry::destroy($id);
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}