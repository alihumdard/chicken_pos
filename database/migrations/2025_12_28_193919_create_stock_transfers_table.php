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
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_shop_id')->constrained('shops');
                $table->foreignId('to_shop_id')->constrained('shops');
                $table->decimal('weight', 10, 3); // Amount in KG
                $table->date('date');
                $table->string('description')->nullable();
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
        Schema::dropIfExists('stock_transfers');
    }
};
