<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     * Store a newly created resource (Supplier or Customer).
     */
    public function store(Request $request)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'name'            => 'required|string|max:255',
                'type'            => 'required|in:supplier,customer',
                'opening_balance' => 'nullable|numeric',
                'phone'           => 'nullable|string|max:20', // Added helpful fields
                'address'         => 'nullable|string|max:255',
            ]);

            $contact = null;

            // 2. SAVE DATA BASED ON TYPE
            if ($validatedData['type'] === 'supplier') {
                $contact = Supplier::create([
                    'name'            => $validatedData['name'],
                    'phone'           => $validatedData['phone'] ?? null,
                    'address'         => $validatedData['address'] ?? null,
                    'current_balance' => $validatedData['opening_balance'] ?? 0,
                ]);
            } else {
                $contact = Customer::create([
                    'name'            => $validatedData['name'],
                    'phone'           => $validatedData['phone'] ?? null,
                    'address'         => $validatedData['address'] ?? null,
                    'current_balance' => $validatedData['opening_balance'] ?? 0,
                ]);
            }

            // Return type for JS
            $contact->type = $validatedData['type'];

            return response()->json([
                'success' => true,
                'message' => ucfirst($validatedData['type']) . ' added successfully!',
                'contact' => $contact,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Request $request, string $id)
    {
        $type = $request->input('type');

        if ($type === 'supplier') {
            $deleted = Supplier::destroy($id);
        } elseif ($type === 'customer') {
            $deleted = Customer::destroy($id);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type.'], 400);
        }

        if ($deleted) {
            return response()->json(['success' => true, 'message' => ucfirst($type) . ' deleted successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Not found.'], 404);
    }

    // ==========================================
    // 🟢 LEDGER & PAYMENT LOGIC
    // ==========================================

    /**
     * Get Ledger for a specific Supplier
     */

    public function getSupplierLedger($id)
    {
        // 1. Supplier dhundain
        $supplier = Supplier::findOrFail($id);

        // 2. Transactions table se data layen
        // Note: Hum 'transactions' table use kar rahe hain jo humne migration se banayi thi
        $transactions = DB::table('transactions')
            ->where('supplier_id', $id)
            ->orderBy('date', 'desc') // Newest date first
            ->orderBy('id', 'desc')   // Newest entry first
            ->limit(50)               // Sirf last 50 records
            ->get();

        // 3. JSON wapis bhejen
        return response()->json([
            // Note: Make sure karen apke database me column ka naam 'current_balance' hi hai
            'current_balance' => $supplier->current_balance ?? 0,
            'transactions'    => $transactions,
        ]);
    }
    public function getCustomerLedger($id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch transactions for CUSTOMER
        $transactions = DB::table('transactions')
            ->where('customer_id', $id) // 🟢 Notice: 'customer_id'
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'current_balance' => $customer->current_balance ?? 0,
            'transactions'    => $transactions,
        ]);
    }
    /**
     * Store a Payment / Transaction
     */
    public function storePayment(Request $request)
    {
        // 1. Determine Validation Rules based on input
        // If 'supplier_id' is present, validate against suppliers table
        // If 'customer_id' is present, validate against customers table

        $rules = [
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
            'type'        => 'required|string',
            'description' => 'nullable|string',
        ];

        if ($request->has('supplier_id')) {
            $rules['supplier_id'] = 'required|exists:suppliers,id'; // 🟢 FIXES "Table not found" error
            $isSupplier           = true;
        } elseif ($request->has('customer_id')) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $isSupplier           = false;
        } else {
            return response()->json(['message' => 'Supplier ID or Customer ID is required'], 422);
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $amount = $request->amount;
            $debit  = 0;
            $credit = 0;
            $desc   = $request->description;

            // 2. Handle Supplier Logic
            if ($isSupplier) {
                $supplier = Supplier::findOrFail($request->supplier_id);

                // Logic: Payment to Supplier reduces the balance (Debit)
                if ($request->type === 'payment') {
                    $debit = $amount;
                    $supplier->decrement('current_balance', $amount); // Reduce debt
                    if (! $desc) {
                        $desc = "Cash Payment";
                    }

                } elseif ($request->type === 'opening_balance') {
                    $credit = $amount; // Increases debt
                    $supplier->increment('current_balance', $amount);
                    if (! $desc) {
                        $desc = "Opening Balance Adjustment";
                    }

                }

                $balance    = $supplier->current_balance;
                $foreignKey = ['supplier_id' => $supplier->id];
            }
            // 3. Handle Customer Logic
            else {
                $customer = Customer::findOrFail($request->customer_id);

                                                    // Logic: Payment FROM Customer reduces balance (Credit)
                if ($request->type === 'payment') { // Receiving money
                    $credit = $amount;
                    $customer->decrement('current_balance', $amount);
                    if (! $desc) {
                        $desc = "Cash Received";
                    }

                } elseif ($request->type === 'opening_balance') { // They owe us
                    $debit = $amount;
                    $customer->increment('current_balance', $amount);
                    if (! $desc) {
                        $desc = "Opening Balance Adjustment";
                    }

                }

                $balance    = $customer->current_balance;
                $foreignKey = ['customer_id' => $customer->id];
            }

            // 4. Record Transaction
            DB::table('transactions')->insert(array_merge($foreignKey, [
                'date'        => $request->date,
                'description' => $desc,
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $balance,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));

            DB::commit();
            return response()->json(['message' => 'Transaction added successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

}
