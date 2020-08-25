<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('shipment_identification_number', 32);
            $table->tinyInteger('package_count');
            $table->decimal('weight', 7, 2);
            $table->string('weight_code', 3);
            $table->string('weight_measurement');
            $table->string('currency_code', 3);
            $table->decimal('total_charges', 8, 2);
            $table->string('status', 8);
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
        Schema::dropIfExists('ups_shipments');
    }
}
