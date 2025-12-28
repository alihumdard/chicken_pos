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
            // Add shop_id to sales
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops');
            });
            // Add shop_id to purchases
            Schema::table('purchases', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops');
            });
            // Add shop_id to users (to know which shop a cashier belongs to)
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->constrained('shops');
            });

            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->constrained('shops');
            });

            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->constrained('shops');
            });
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases_sales_users', function (Blueprint $table) {
            //
        });
    }
};
