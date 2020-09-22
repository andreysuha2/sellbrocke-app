<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("merchant_id");
            $table->bigInteger("merchant_customer_id");
            $table->string("email");
            $table->string("login");
            $table->string("first_name", 64)->nullable();
            $table->string("last_name", 64)->nullable();
            $table->string("address")->nullable();
            $table->string("city", 64)->nullable();
            $table->string("state", 2)->nullable();
            $table->string("zip", 16)->nullable();
            $table->string("phone", 32)->nullable();
            $table->string("paypal_email")->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(["merchant_id", "merchant_customer_id", "email"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
