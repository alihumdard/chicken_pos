<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_transfer_id')->nullable()->after('customer_id');
            $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['stock_transfer_id']);
            $table->dropColumn('stock_transfer_id');
        });
    }
};