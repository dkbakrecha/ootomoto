<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefunds extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id');
            $table->integer('shop_id');
            $table->integer('customer_id');
            $table->string('transaction_id')->nullable()->comment = "Actual telr transaction ID for refund";
            $table->string('unique_id')->nullable();
            $table->dateTime('refund_date')->nullable();
            $table->float('amount', 8, 2);
            $table->text('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('refunds');
    }

}
