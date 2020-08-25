<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFedexShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fedex_shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('job_id', 32);
            $table->string('tracking_type', 16);
            $table->string('tracking_number', 32);
            $table->decimal('weight', 7, 2);
            $table->string('weight_code', 3);
            $table->string('currency_code', 3);
            $table->decimal('total_charges', 8, 2);
            $table->string('status', 8);
            $table->text('label');
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
        Schema::dropIfExists('fedex_shipments');
    }
}
