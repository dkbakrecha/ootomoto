<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShopWorkingHours extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('shop_working_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->unsigned(); //For User ID
            $table->foreign('shop_id')->references('id')->on('users');
            $table->integer('is_open')->default(1);
            $table->string('shop_weekday');
            $table->string('shop_starttime');
            $table->string('shop_closetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shop_working_hours');
    }

}
