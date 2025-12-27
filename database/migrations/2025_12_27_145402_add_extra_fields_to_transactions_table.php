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
    Schema::table('transactions', function (Blueprint $table) {
        $table->decimal('gross_weight', 10, 2)->nullable()->after('description');
        $table->decimal('dead_weight', 10, 2)->nullable()->after('gross_weight');
        $table->decimal('shrink_loss', 10, 2)->nullable()->after('dead_weight');
        $table->decimal('net_live_weight', 10, 2)->nullable()->after('shrink_loss');
        $table->decimal('total_kharch', 12, 2)->nullable()->after('net_live_weight');
        $table->decimal('buying_rate', 10, 2)->nullable()->after('total_kharch');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
