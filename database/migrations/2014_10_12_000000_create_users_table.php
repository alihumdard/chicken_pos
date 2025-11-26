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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('role')->nullable();
            $table->string('user_pic')->nullable();
            $table->string('com_name')->nullable();
            $table->string('com_pic')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->integer('otp')->nullable();
            $table->string('reset_pswd_time')->nullable();
            $table->string('reset_pswd_attempt')->nullable();
            $table->boolean('subscribed_to_newsletter')->nullable()->default(false);;
            $table->timestamp('terms_accepted_at')->nullable();            
            $table->timestamp('privacy_policy_accepted_at')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable()->default(null);
            $table->unsignedBigInteger('sadmin_id')->nullable()->default(null);
            $table->string('status')->default('2');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
