<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShopReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->unsigned(); //For User ID
            $table->foreign('shop_id')->references('id')->on('users');
            $table->integer('booking_id')->unsigned(); //For User ID
            $table->integer('customer_id');
            $table->integer('rating');
            $table->text('review_text');
            $table->integer('is_flagged')->default(0)->comment = "0 = Normal, 1 = Flagged by shop";
            $table->integer('status')->default(0)->comment = "0 = Pending, 1 = Approved, 2 = Rejected";
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
        Schema::dropIfExists('shop_reviews');
    }
}
