<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceipts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id');
            $table->integer('shop_id');
            $table->integer('customer_id');
            $table->string('unique_id')->nullable();
            $table->string('transaction_id')->nullable()->comment = "Actual telr transaction ID";
            $table->dateTime('receipt_date')->nullable();
            $table->string('services');
            $table->float('final_amount', 8, 2);
            $table->integer('payment_method')->default(1)->comment = "1 = cash, 2 = card";
            $table->string('barber_id')->nullable();
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
        Schema::dropIfExists('receipts');
    }
}
