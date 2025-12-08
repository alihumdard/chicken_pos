<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule; // 🟢 Import Rule for cleaner validation

class SupplierCustomerController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        $customers = Customer::all();
        return view('pages.supplier.index', compact('suppliers', 'customers'));
    }

    /**
     * Store a newly created resource.
     */
 public function store(Request $request)
    {
        try {
            $tableName = ($request->type === 'supplier') ? 'suppliers' : 'customers';

            $validatedData = $request->validate([
                'name'            => 'required|string|max:255', 
                'type'            => 'required|in:supplier,customer',
                'opening_balance' => 'nullable|numeric',
                'phone'           => "nullable|string|max:20|unique:$tableName,phone", 
                'address'         => 'nullable|string|max:255',
            ]);

            $contact = null;
            $openingBal = $validatedData['opening_balance'] ?? 0;

            DB::beginTransaction(); // Start Transaction

            if ($validatedData['type'] === 'supplier') {
                $contact = Supplier::create([
                    'name'            => $validatedData['name'],
                    'phone'           => $validatedData['phone'] ?? null,
                    'address'         => $validatedData['address'] ?? null,
                    'current_balance' => $openingBal,
                ]);
            } else {
                $contact = Customer::create([
                    'name'            => $validatedData['name'],
                    'phone'           => $validatedData['phone'] ?? null,
                    'address'         => $validatedData['address'] ?? null,
                    'current_balance' => $openingBal,
                ]);
            }

            // 🟢 NEW: Add Opening Balance to Ledger/Transactions automatically
            if ($openingBal != 0) {
                $debit = 0;
                $credit = 0;

                // For Suppliers: Positive Balance = Credit (We owe them)
                // For Customers: Positive Balance = Debit (They owe us)
                if ($validatedData['type'] === 'supplier') {
                    if ($openingBal > 0) $credit = $openingBal;
                    else $debit = abs($openingBal);
                } else {
                    if ($openingBal > 0) $debit = $openingBal;
                    else $credit = abs($openingBal);
                }

                DB::table('transactions')->insert([
                    'supplier_id' => ($validatedData['type'] === 'supplier') ? $contact->id : null,
                    'customer_id' => ($validatedData['type'] === 'customer') ? $contact->id : null,
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

            DB::commit(); // Save everything

            $contact->type = $validatedData['type'];

            return response()->json([
                'success' => true,
                'message' => ucfirst($validatedData['type']) . ' added successfully!',
                'contact' => $contact,
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        try {
            // 🟢 Determine table name for validation
            $tableName = ($request->type === 'supplier') ? 'suppliers' : 'customers';

            // 1. Validate Input
            $validatedData = $request->validate([
                'name'    => 'required|string|max:255',
                'type'    => 'required|in:supplier,customer',
                // 🟢 Unique Phone but ignore current ID (so you can edit name without changing phone)
                'phone'   => "nullable|string|max:20|unique:$tableName,phone,$id", 
                'address' => 'nullable|string|max:255',
            ]);

            $contact = null;

            if ($validatedData['type'] === 'supplier') {
                $contact = Supplier::findOrFail($id);
                $contact->update([
                    'name'    => $validatedData['name'],
                    'phone'   => $validatedData['phone'] ?? null,
                    'address' => $validatedData['address'] ?? null,
                ]);
            } else {
                $contact = Customer::findOrFail($id);
                $contact->update([
                    'name'    => $validatedData['name'],
                    'phone'   => $validatedData['phone'] ?? null,
                    'address' => $validatedData['address'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => ucfirst($validatedData['type']) . ' updated successfully!'
            ]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

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

    // ... Ledger and Payment methods remain exactly the same as your previous code ...
    // Copy the getSupplierLedger, getCustomerLedger, and storePayment methods here.
    
    public function getSupplierLedger($id) {
        $supplier = Supplier::findOrFail($id);
        $transactions = DB::table('transactions')
            ->where('supplier_id', $id)
            ->orderBy('date', 'desc')->orderBy('id', 'desc')->limit(50)->get();
        return response()->json(['current_balance' => $supplier->current_balance ?? 0, 'transactions' => $transactions]);
    }

    public function getCustomerLedger($id) {
        $customer = Customer::findOrFail($id);
        $transactions = DB::table('transactions')
            ->where('customer_id', $id)
            ->orderBy('date', 'desc')->orderBy('id', 'desc')->limit(50)->get();
        return response()->json(['current_balance' => $customer->current_balance ?? 0, 'transactions' => $transactions]);
    }

    public function storePayment(Request $request) {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ];
        if ($request->has('supplier_id')) {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $isSupplier = true;
        } elseif ($request->has('customer_id')) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $isSupplier = false;
        } else {
            return response()->json(['message' => 'ID required'], 422);
        }
        $request->validate($rules);

        DB::beginTransaction();
        try {
            $amount = $request->amount;
            $debit = 0; $credit = 0; $desc = $request->description;

            if ($isSupplier) {
                $supplier = Supplier::findOrFail($request->supplier_id);
                if ($request->type === 'payment') {
                    $debit = $amount;
                    $supplier->decrement('current_balance', $amount);
                    if (!$desc) $desc = "Cash Payment";
                } elseif ($request->type === 'opening_balance') {
                    $credit = $amount;
                    $supplier->increment('current_balance', $amount);
                    if (!$desc) $desc = "Opening Balance Adjustment";
                }
                $balance = $supplier->current_balance;
                $foreignKey = ['supplier_id' => $supplier->id];
            } else {
                $customer = Customer::findOrFail($request->customer_id);
                if ($request->type === 'payment') {
                    $credit = $amount;
                    $customer->decrement('current_balance', $amount);
                    if (!$desc) $desc = "Cash Received";
                } elseif ($request->type === 'opening_balance') {
                    $debit = $amount;
                    $customer->increment('current_balance', $amount);
                    if (!$desc) $desc = "Opening Balance Adjustment";
                }
                $balance = $customer->current_balance;
                $foreignKey = ['customer_id' => $customer->id];
            }

            DB::table('transactions')->insert(array_merge($foreignKey, [
                'date' => $request->date, 'description' => $desc, 'debit' => $debit,
                'credit' => $credit, 'balance' => $balance,
                'created_at' => now(), 'updated_at' => now(),
            ]));

            DB::commit();
            return response()->json(['message' => 'Transaction added successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}