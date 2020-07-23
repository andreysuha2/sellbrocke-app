<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceProductGridTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_product_grid', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("device_id");
            $table->bigInteger("product_grid_id");
            $table->unique([ "device_id", "product_grid_id" ], "unique_bundle");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_product_grid');
    }
}
