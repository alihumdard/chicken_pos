<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // 🟢 IMPORTED for explicit exception handling

class SupplierCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all(); 
        $customers = Customer::all(); 

        return view('pages.supplier.index', compact('suppliers', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method usually shows the form. Since you are using a modal on the index page, 
        // this might not be needed, but we keep it returning the index view for consistency.
        return view('pages.supplier.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🛑 FIX: The client-side relies on a JSON response. 
        // We ensure we only try/catch and return JSON here.
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:supplier,customer',
                'opening_balance' => 'nullable|numeric',
            ]);
            
            $contact = null;

            // 2. SAVE DATA BASED ON TYPE
            if ($validatedData['type'] === 'supplier') {
                $contact = Supplier::create([
                    'name' => $validatedData['name'],
                    'current_balance' => $validatedData['opening_balance'] ?? 0, 
                ]);
            } else {
                $contact = Customer::create([
                    'name' => $validatedData['name'],
                    'current_balance' => $validatedData['opening_balance'] ?? 0, 
                ]);
            }

            // 🟢 CRITICAL STEP: Add the 'type' field to the contact object 
            // for the JavaScript appendContactToList function to use.
            $contact->type = $validatedData['type']; 

            // 3. RETURN JSON: Return the created contact object for JavaScript
            return response()->json([
                'success' => true,
                'message' => ucfirst($validatedData['type']) . ' added successfully!',
                'contact' => $contact, 
            ], 201); // 201 Created status - prevents browser reload

        } catch (ValidationException $e) {
            // Handle validation errors (422)
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Handle other errors (500)
            return response()->json([
                'success' => false,
                'message' => 'Could not save the contact. Error: ' . $e->getMessage(), // Added error message for debugging
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Get the type from the request body sent by AJAX
        $type = $request->input('type');

        if ($type === 'supplier') {
            $deleted = Supplier::destroy($id);
        } elseif ($type === 'customer') {
            $deleted = Customer::destroy($id);
        } else {
             return response()->json([
                'success' => false,
                'message' => 'Invalid contact type provided.',
            ], 400); // 400 Bad Request
        }

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully!',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => ucfirst($type) . ' not found or could not be deleted.',
        ], 404); // 404 Not Found
    }
}