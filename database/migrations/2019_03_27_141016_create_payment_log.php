<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id');
            $table->integer('payment_type')->default(0)->comment = "0 = Capture, 1 = Refund";
            $table->text('payment_log');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_log');
    }

}
