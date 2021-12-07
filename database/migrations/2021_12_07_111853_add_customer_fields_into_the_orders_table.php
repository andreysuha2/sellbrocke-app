<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerFieldsIntoTheOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
           $table->string('first_name', 64)->after('customer_id')->nullable();
           $table->string('last_name', 64)->after('first_name')->nullable();
           $table->string('address', 191)->after('last_name')->nullable();
           $table->string('city', 64)->after('address')->nullable();
           $table->string('state', 2)->after('city')->nullable();
           $table->string('zip', 16)->after('state')->nullable();
           $table->string('phone', 32)->after('zip')->nullable();
           $table->string('paypal_email', 191)->after('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zip');
            $table->dropColumn('phone');
            $table->dropColumn('paypal_email');
        });
    }
}
