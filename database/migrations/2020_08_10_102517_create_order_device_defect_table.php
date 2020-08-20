<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDeviceDefectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_device_defect', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("order_device_id");
            $table->bigInteger("defect_id");
            $table->unique([ "order_device_id", "defect_id" ], "unique_bundle");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_device_defect');
    }
}
