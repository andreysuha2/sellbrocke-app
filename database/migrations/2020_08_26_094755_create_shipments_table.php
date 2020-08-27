<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("order_id");
            $table->enum("type", [ "FEDEX", "UPS" ]);
            $table->string("tracking_number");
            $table->decimal("weight", 5, 2);
            $table->string("weight_code", 3);
            $table->decimal("total_charges", 8, 2);
            $table->string("currency_code", 3);
            $table->string("status");
            $table->json("data");
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
        Schema::dropIfExists('shipments');
    }
}
