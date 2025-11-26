<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->string('title');
            $table->string('name');
            $table->string('email');
            $table->string('email_forward')->default('n');
            $table->string('type');
            $table->string('body');
            $table->text('message');
            $table->string('url');
            $table->dateTime('display_date')->nullable();
            $table->string('deleted_at');
            $table->string('status')->default('unseen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
