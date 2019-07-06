<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->integer('shop_id');
            $table->string('title');
            $table->text('description');
            $table->string('services');
            $table->string('offer_image')->nullable();
            $table->integer('price')->default(0);
            $table->integer('days')->default(0);
            $table->integer('status')->default(3)->comment = "0 = inactive, 1 = active, 2 = delete, 3 = pending, 4 = expire";
            $table->dateTime('expire_date')->nullable();
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
        Schema::dropIfExists('shop_offers');
    }
}
