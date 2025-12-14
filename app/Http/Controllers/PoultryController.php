<?php

namespace App\Http\Controllers;

use App\Models\Poultry;
use Illuminate\Http\Request;
use Exception;

class PoultryController extends Controller
{
    public function index()
    {
        $poultries = Poultry::orderBy('entry_date', 'desc')->get();
        return view('pages.poultry.index', compact('poultries'));
    }

    public function store(Request $request)
    {
        // 1. Validate Input
        $data = $request->validate([
            'entry_date'   => 'required|date',
            'batch_no'     => 'nullable|string|max:50',
            'quantity'     => 'nullable|integer|min:0',
            'total_weight' => 'required|numeric|min:0',
            'cost_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        try {
            // 🟢 Fix: Agar Quantity null/empty ho to 0 set karein
            if (!isset($data['quantity']) || $data['quantity'] === null) {
                $data['quantity'] = 0;
            }

            // 2. Create Record
            Poultry::create($data);

            return response()->json(['success' => true, 'message' => 'Added successfully!'], 201);

        } catch (Exception $e) {
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
        $data = $request->validate([
            'entry_date'   => 'required|date',
            'batch_no'     => 'nullable|string|max:50',
            'quantity'     => 'nullable|integer|min:0',
            'total_weight' => 'required|numeric|min:0',
            'cost_price'   => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        try {
            $poultry = Poultry::findOrFail($id);

            // 🟢 Fix: Update mein bhi agar quantity empty ho to 0 karein
            if (!isset($data['quantity']) || $data['quantity'] === null) {
                $data['quantity'] = 0;
            }

            $poultry->update($data);

            return response()->json(['success' => true, 'message' => 'Updated successfully!']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Poultry::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting record'], 500);
        }
    }
}