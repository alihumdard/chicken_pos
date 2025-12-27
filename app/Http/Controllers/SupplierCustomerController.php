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
            // Agar input 'supplier' hai to Supplier table, varna Customer table
            $inputType  = $request->type;
            $isSupplier = ($inputType === 'supplier');
            $tableName  = $isSupplier ? 'suppliers' : 'customers';
            $modelClass = $isSupplier ? Supplier::class : Customer::class;

            // 2. Validate
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'type'            => 'required|string|max:50',
                'opening_balance' => 'nullable|numeric',
                'phone'           => "nullable|string|max:20|unique:$tableName,phone",
                'address'         => 'nullable|string|max:255',
            ]);

            $openingBal = (float) ($validated['opening_balance'] ?? 0);

            DB::beginTransaction();

            // 3. Create Contact
            $contact = $modelClass::create([
                'name'            => $validated['name'],
                'type'            => $validated['type'], // Custom types (broker, shop_retail etc)
                'phone'           => $validated['phone'] ?? null,
                'address'         => $validated['address'] ?? null,
                'current_balance' => $openingBal,
            ]);

            // 4. Add Opening Balance to Ledger (Transactions Table)
            // Opening balance ko hamesha pehli transaction ke taur par record karna chahiye
            if ($openingBal != 0) {
                $debit  = 0;
                $credit = 0;

                if ($isSupplier) {
                    // Supplier ke liye: Positive Balance = Humne dena hai (Credit)
                    // Negative Balance = Advance diya hua hai (Debit)
                    if ($openingBal > 0) {
                        $credit = $openingBal;
                    } else {
                        $debit = abs($openingBal);
                    }
                } else {
                    // Customer ke liye: Positive Balance = Usne dena hai (Debit)
                    // Negative Balance = Uska advance aaya hua hai (Credit)
                    if ($openingBal > 0) {
                        $debit = $openingBal;
                    } else {
                        $credit = abs($openingBal);
                    }
                }

                DB::table('transactions')->insert([
                    'supplier_id' => $isSupplier ? $contact->id : null,
                    'customer_id' => ! $isSupplier ? $contact->id : null,
                    'date'        => now(),
                    'type'        => 'opening_balance',
                    'description' => 'Opening Balance Entry',
                    'debit'       => $debit,
                    'credit'      => $credit,
                    'balance'     => $openingBal,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact added successfully!',
                    'contact' => [
                        'id'              => $contact->id,
                        'name'            => $contact->name,
                        'type'            => $contact->type,
                        'current_balance' => $contact->current_balance,
                    ],
                ], 201);
            }

            return redirect()->back()->with('success', 'Added successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $isSupplier = $request->type === 'supplier';
            $tableName  = $isSupplier ? 'suppliers' : 'customers';
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
                'contact' => $contact,
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $type       = $request->input('type');
            $modelClass = ($type === 'supplier') ? Supplier::class : Customer::class;
            $foreignKey = ($type === 'supplier') ? 'supplier_id' : 'customer_id';

            DB::beginTransaction();

            // 1. Delete Ledger Transactions
            DB::table('transactions')->where($foreignKey, $id)->delete();

            // 2. Specific Logic for Customers (Delete Sales and Items)
            if ($type === 'customer' || $type === 'broker' || $type === 'shop_retail') {
                // Find all sales for this customer
                $saleIds = DB::table('sales')->where('customer_id', $id)->pluck('id');

                // Delete items belonging to those sales
                DB::table('sale_items')->whereIn('sale_id', $saleIds)->delete();

                // Delete the sales records themselves
                DB::table('sales')->where('customer_id', $id)->delete();
            }

            // 3. Delete the contact
            $contact = $modelClass::findOrFail($id);
            $contact->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' and all related history/sales deleted successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // --- Ledger Methods ---

   public function getSupplierLedger($id)
{
    $supplier = Supplier::findOrFail($id);

    $transactions = DB::table('transactions')
        ->leftJoin('purchases', function ($join) {
            $join->on(DB::raw("SUBSTRING_INDEX(transactions.description, '#', -1)"), '=', DB::raw("purchases.id"))
                ->where('transactions.type', '=', 'purchase');
        })
        ->where('transactions.supplier_id', $id)
        ->select(
            DB::raw("CASE 
                WHEN transactions.description LIKE '%Purchase #%' THEN SUBSTRING_INDEX(transactions.description, '#', -1)
                ELSE transactions.id 
            END as group_key"),
            DB::raw('MIN(transactions.date) as date'),
            DB::raw('MAX(transactions.description) as description'),
            DB::raw('SUM(transactions.debit) as debit'),
            DB::raw('SUM(transactions.credit) as credit'),
            'purchases.gross_weight',
            'purchases.net_live_weight',
            'purchases.buying_rate',
            'purchases.total_kharch'
        )
        ->groupBy('group_key', 'purchases.gross_weight', 'purchases.net_live_weight', 'purchases.buying_rate', 'purchases.total_kharch')
        ->orderBy('date', 'asc')
        ->get();

    return response()->json([
        'current_balance' => $supplier->current_balance,
        'transactions'    => $transactions,
    ]);
}

    public function getCustomerLedger($id)
{
    try {
        $contact = Customer::findOrFail($id);

        /**
         * 🟢 UPDATED LOGIC:
         * 1. Hum description mein se '#' ke baad wala number nikaalte hain.
         * 2. Hum grouping ko hamesha 'group_key' par karte hain.
         * 3. Agar description mein '#' nahi milta (jaise opening balance), toh unique ID use karte hain.
         */
        $transactions = DB::table('transactions')
            ->where('customer_id', $id)
            ->select(
                DB::raw("CASE 
                    WHEN description LIKE '%#%' THEN SUBSTRING_INDEX(SUBSTRING_INDEX(description, '#', -1), ' ', 1)
                    ELSE CAST(id AS CHAR) 
                END as group_key"),
                DB::raw('MIN(date) as date'),
                DB::raw('MAX(description) as description'),
                DB::raw('SUM(debit) as debit'),
                DB::raw('SUM(credit) as credit'),
                DB::raw('MIN(id) as sort_id')
            )
            ->groupBy('group_key')
            ->orderBy('date', 'asc')
            ->orderBy('sort_id', 'asc')
            ->get();

        return response()->json([
            'success'         => true,
            'current_balance' => $contact->current_balance ?? 0,
            'transactions'    => $transactions,
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
            $isSupplier           = true;
        } elseif ($request->has('customer_id')) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $isSupplier           = false;
        } else {
            return response()->json(['message' => 'Customer or Supplier ID required'], 422);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $amount  = $validated['amount'];
            $desc    = $validated['description'];
            $txnType = $validated['type'];

            // Get Model
            if ($isSupplier) {
                $contact    = Supplier::lockForUpdate()->find($validated['supplier_id']); // Lock row to prevent race conditions
                $foreignKey = ['supplier_id' => $contact->id];
            } else {
                $contact    = Customer::lockForUpdate()->find($validated['customer_id']);
                $foreignKey = ['customer_id' => $contact->id];
            }

            $debit  = 0;
            $credit = 0;

            // Logic Determination
            if ($isSupplier) {
                // SUPPLIER LOGIC
                if ($txnType === 'payment') {
                    // We pay Supplier -> Balance decreases (Debit Transaction in Ledger, but logically we are paying debt)
                    // Usually in Accounting: Supplier Dr (Debit) | Cash Cr (Credit)
                    $debit = $amount;
                    $contact->decrement('current_balance', $amount);
                    if (! $desc) {
                        $desc = "-";
                    }

                } elseif ($txnType === 'opening_balance' || $txnType === 'adjustment') {
                    // We owe more -> Balance increases
                    $credit = $amount;
                    $contact->increment('current_balance', $amount);
                    if (! $desc) {
                        $desc = "-";
                    }

                }
            } else {
                // CUSTOMER LOGIC
                if ($txnType === 'payment') {
                    // Customer pays Us -> Balance decreases
                    // Usually: Cash Dr | Customer Cr
                    $credit = $amount;
                    $contact->decrement('current_balance', $amount);
                    if (! $desc) {
                        $desc = "-";
                    }

                } elseif ($txnType === 'opening_balance' || $txnType === 'adjustment') {
                    // Customer owes more -> Balance increases
                    $debit = $amount;
                    $contact->increment('current_balance', $amount);
                    if (! $desc) {
                        $desc = "-";
                    }

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
                'success'     => true,
                'message'     => 'Transaction added successfully',
                'new_balance' => $newBalance,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // 1. Ledger Entry Update Method
    public function updateLedger(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Transaction dhoondein
            $transaction = DB::table('transactions')->where('id', $id)->first();
            if (! $transaction) {
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            // Purane values calculate karein balance update ke liye
            $oldDebit  = (float) $transaction->debit;
            $oldCredit = (float) $transaction->credit;

            // Naye values
            $newDebit  = (float) $request->debit;
            $newCredit = (float) $request->credit;

            // Transaction Update karein
            DB::table('transactions')->where('id', $id)->update([
                'date'        => $request->date,
                'description' => $request->description,
                'debit'       => $newDebit,
                'credit'      => $newCredit,
                'updated_at'  => now(),
            ]);

            // 🟢 Balance Recalculation Logic (Har row ke liye)
            $this->recalculateBalance($transaction->customer_id, $transaction->supplier_id);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ledger updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

// 2. Ledger Entry Delete Method
    public function destroyLedger($id)
    {
        try {
            DB::beginTransaction();

            $transaction = DB::table('transactions')->where('id', $id)->first();
            if (! $transaction) {
                return response()->json(['success' => false, 'message' => 'Record not found'], 404);
            }

            $customerId = $transaction->customer_id;
            $supplierId = $transaction->supplier_id;

            // Delete karein
            DB::table('transactions')->where('id', $id)->delete();

            // 🟢 Balance Recalculation
            $this->recalculateBalance($customerId, $supplierId);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaction deleted and balance updated']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

/**
 * 🟢 Private function jo balance ko dobara calculate karegi
 * Isme DB::selectRaw (Fixed syntax) use kiya gaya hai
 */
    private function recalculateBalance($customerId, $supplierId)
    {
        if ($customerId) {
            // Customer balance logic: Total Debits - Total Credits
            $totals = DB::table('transactions')
                ->where('customer_id', $customerId)
                ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
                ->first();

            $newBalance = ($totals->total_debit ?? 0) - ($totals->total_credit ?? 0);
            DB::table('customers')->where('id', $customerId)->update(['current_balance' => $newBalance]);
        } elseif ($supplierId) {
            // Supplier balance logic: Total Credits - Total Debits
            $totals = DB::table('transactions')
                ->where('supplier_id', $supplierId)
                ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
                ->first();

            $newBalance = ($totals->total_credit ?? 0) - ($totals->total_debit ?? 0);
            DB::table('suppliers')->where('id', $supplierId)->update(['current_balance' => $newBalance]);
        }
    }
}
