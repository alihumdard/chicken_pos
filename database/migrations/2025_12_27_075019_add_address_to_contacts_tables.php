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
       Schema::table('suppliers', function (Blueprint $table) {
        $table->string('address')->nullable()->after('phone');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts_tables', function (Blueprint $table) {
          Schema::table('suppliers', function ($table) { $table->dropColumn('address'); });
        });
    }
};
