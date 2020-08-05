<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchSlugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_slugs', function (Blueprint $table) {
            $table->id();
            $table->string("slug", 500)->unique();
            $table->string("category_part", 500);
            $table->string("company_part", 500)->nullable();
            $table->string("device_part", 500)->nullable();
            $table->morphs("search");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_slugs');
    }
}
