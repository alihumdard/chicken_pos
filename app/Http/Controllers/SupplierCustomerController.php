<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class SupplierCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all records ordered by newest first
        $suppliers = Supplier::orderBy('created_at', 'desc')->get();
        $customers = Customer::orderBy('created_at', 'desc')->get();
        return view('pages.supplier.index', compact('suppliers', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. Determine Type & Model
            $isSupplier = $request->type === 'supplier';
            $tableName = $isSupplier ? 'suppliers' : 'customers';
            $modelClass = $isSupplier ? Supplier::class : Customer::class;

            // 2. Validate
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'type'            => 'required|in:supplier,customer',
                'opening_balance' => 'nullable|numeric',
                'phone'           => "nullable|string|max:20|unique:$tableName,phone",
                'address'         => 'nullable|string|max:255',
            ]);

            $openingBal = $validated['opening_balance'] ?? 0;

            DB::beginTransaction(); // Start Transaction

            // 3. Create Contact
            $contact = $modelClass::create([
                'name'            => $validated['name'],
                'phone'           => $validated['phone'] ?? null,
                'address'         => $validated['address'] ?? null,
                'current_balance' => $openingBal,
            ]);

            // 4. Add Opening Balance to Ledger (Transactions Table)
            if ($openingBal != 0) {
                $debit = 0;
                $credit = 0;

                // Logic:
                // Supplier: Positive Balance = Credit (Udhaar/Payable)
                // Customer: Positive Balance = Debit (Lena hai/Receivable)
                if ($isSupplier) {
                    if ($openingBal > 0) $credit = $openingBal;
                    else $debit = abs($openingBal);
                } else {
                    if ($openingBal > 0) $debit = $openingBal;
                    else $credit = abs($openingBal);
                }

                DB::table('transactions')->insert([
                    'supplier_id' => $isSupplier ? $contact->id : null,
                    'customer_id' => !$isSupplier ? $contact->id : null,
                    'date'        => now(),
                    'type'        => 'opening_balance',
                    'description' => 'Opening Balance',
                    'debit'       => $debit,
                    'credit'      => $credit,
                    'balance'     => $openingBal,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit(); // Save changes

            // 🟢 JSON Response tailored for your AJAX Frontend
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($validated['type']) . ' added successfully!',
                    // Returning these direct keys for your JS logic:
                    'id'              => $contact->id,
                    'name'            => $contact->name,
                    'opening_balance' => $contact->current_balance
                ], 201);
            }

            return redirect()->back()->with('success', 'Contact added successfully');

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $isSupplier = $request->type === 'supplier';
            $tableName = $isSupplier ? 'suppliers' : 'customers';
            $modelClass = $isSupplier ? Supplier::class : Customer::class;

            // 1. Validate
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'type'    => 'required|in:supplier,customer',
                'phone'   => "nullable|string|max:20|unique:$tableName,phone,$id",
                'address' => 'nullable|string|max:255',
            ]);

            // 2. Update
            $contact = $modelClass::findOrFail($id);
            $contact->update([
                'name'    => $validated['name'],
                'phone'   => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' updated successfully!',
                'contact' => $contact
            ]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $type = $request->input('type');
            $modelClass = ($type === 'supplier') ? Supplier::class : (($type === 'customer') ? Customer::class : null);

            if (!$modelClass) {
                return response()->json(['success' => false, 'message' => 'Invalid type.'], 400);
            }

            // Optional: Check if transactions exist before deleting
            $hasTransactions = DB::table('transactions')
                ->where($type === 'supplier' ? 'supplier_id' : 'customer_id', $id)
                ->exists();

            if ($hasTransactions) {
                return response()->json(['success' => false, 'message' => 'Cannot delete: This contact has transaction history.'], 403);
            }

            $contact = $modelClass::findOrFail($id);
            $contact->delete();

            return response()->json(['success' => true, 'message' => ucfirst($type) . ' deleted successfully!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()], 500);
        }
    }

    // --- Ledger Methods ---

    public function getSupplierLedger($id)
    {
        return $this->getLedger($id, 'supplier_id', Supplier::class);
    }

    public function getCustomerLedger($id)
    {
        return $this->getLedger($id, 'customer_id', Customer::class);
    }

    /**
     * Helper function to reduce code duplication in ledger fetching
     */
    private function getLedger($id, $foreignKey, $modelClass)
    {
        try {
            $contact = $modelClass::findOrFail($id);
            
            $transactions = DB::table('transactions')
                ->where($foreignKey, $id)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(100) // Increased limit slightly
                ->get();

            return response()->json([
                'success' => true,
                'current_balance' => $contact->current_balance ?? 0,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching ledger'], 500);
        }
    }

    /**
     * Store Payment / Adjust Balance
     */
    public function storePayment(Request $request)
    {
        // 1. Conditional Validation Rules
        $rules = [
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'type'        => 'required|string|in:payment,opening_balance,adjustment',
            'description' => 'nullable|string',
        ];

        if ($request->has('supplier_id')) {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $isSupplier = true;
        } elseif ($request->has('customer_id')) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $isSupplier = false;
        } else {
            return response()->json(['message' => 'Customer or Supplier ID required'], 422);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $amount = $validated['amount'];
            $desc = $validated['description'];
            $txnType = $validated['type'];
            
            // Get Model
            if ($isSupplier) {
                $contact = Supplier::lockForUpdate()->find($validated['supplier_id']); // Lock row to prevent race conditions
                $foreignKey = ['supplier_id' => $contact->id];
            } else {
                $contact = Customer::lockForUpdate()->find($validated['customer_id']);
                $foreignKey = ['customer_id' => $contact->id];
            }

            $debit = 0;
            $credit = 0;

            // Logic Determination
            if ($isSupplier) {
                // SUPPLIER LOGIC
                if ($txnType === 'payment') {
                    // We pay Supplier -> Balance decreases (Debit Transaction in Ledger, but logically we are paying debt)
                    // Usually in Accounting: Supplier Dr (Debit) | Cash Cr (Credit)
                    $debit = $amount; 
                    $contact->decrement('current_balance', $amount);
                    if (!$desc) $desc = "Cash Payment to Supplier";
                } elseif ($txnType === 'opening_balance' || $txnType === 'adjustment') {
                    // We owe more -> Balance increases
                    $credit = $amount; 
                    $contact->increment('current_balance', $amount);
                    if (!$desc) $desc = "Balance Adjustment (Credit)";
                }
            } else {
                // CUSTOMER LOGIC
                if ($txnType === 'payment') {
                    // Customer pays Us -> Balance decreases
                    // Usually: Cash Dr | Customer Cr
                    $credit = $amount; 
                    $contact->decrement('current_balance', $amount);
                    if (!$desc) $desc = "Cash Received from Customer";
                } elseif ($txnType === 'opening_balance' || $txnType === 'adjustment') {
                    // Customer owes more -> Balance increases
                    $debit = $amount; 
                    $contact->increment('current_balance', $amount);
                    if (!$desc) $desc = "Balance Adjustment (Debit)";
                }
            }

            // Refresh balance after increment/decrement
            $contact->refresh();
            $newBalance = $contact->current_balance;

            // Insert Transaction
            DB::table('transactions')->insert(array_merge($foreignKey, [
                'date'        => $validated['date'],
                'type'        => $txnType,
                'description' => $desc,
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $newBalance, // Snapshot of balance at this time
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction added successfully',
                'new_balance' => $newBalance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}