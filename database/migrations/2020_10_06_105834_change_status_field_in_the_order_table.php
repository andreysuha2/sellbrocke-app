<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusFieldInTheOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('pending', 'open', 'closed', 'cancelled') DEFAULT 'pending' NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('open', 'closed', 'canceled') DEFAULT 'open' NOT NULL");
        });
    }
}
