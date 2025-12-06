<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        
        // Link to Suppliers (nullable because a transaction might belong to a customer instead)
        $table->unsignedBigInteger('supplier_id')->nullable();
        $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

        // Link to Customers (nullable)
        $table->unsignedBigInteger('customer_id')->nullable();
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        $table->date('date');
        $table->string('type')->nullable(); // e.g., 'purchase', 'payment', 'opening_balance'
        $table->string('description')->nullable();
        
        // Financials (Using Decimal for money precision)
        $table->decimal('debit', 15, 2)->default(0);  // Money In / Owed
        $table->decimal('credit', 15, 2)->default(0); // Money Out / Paid
        $table->decimal('balance', 15, 2)->default(0); // Running Balance at that time
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
