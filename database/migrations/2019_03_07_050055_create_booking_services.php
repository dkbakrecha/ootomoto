<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id');
            $table->integer('service_id');
            $table->integer('barber_id');
            $table->string('starttime');
            $table->string('endtime');
            $table->integer('status')->default(0)->comment = "1 = complete, 0 = not complete";
            $table->integer('shop_id')->default(0);
            $table->float('price', 8, 2)->default(0);
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
        Schema::dropIfExists('booking_services');
    }
}
