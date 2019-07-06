<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->nullable();
            $table->integer('shop_id');
            $table->integer('customer_id');
            $table->integer('booking_mode')->default(0)->comment = "0 = online/via app, 1 = offline/walkins";;
            $table->dateTime('booking_date')->nullable();
            $table->string('booking_starttime');
            $table->string('booking_endtime');
            $table->float('sub_total', 8, 2);
            $table->float('vat_amount', 8, 2)->default(0);
            $table->float('offer_amount', 8, 2)->default(0);
            $table->float('final_amount', 8, 2);
            $table->integer('area_id')->default(0)->comment = "Service Providers's area";
            $table->integer('commission_type')->default(0)->comment = "0 = %, 1 = Fixed";
            $table->float('commission_amount', 8, 2)->default(0);
            $table->integer('payment_method')->default(1)->comment = "1 = cash, 2 = card";
            $table->integer('is_reschedule')->default(0)->comment = "0 = Normal, 1 = reschedule";
            $table->integer('reschedule_amount')->default(0)->comment = "> 0 Clint need to pay difference";
            $table->dateTime('cancel_date')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->float('cancellation_amount', 8, 2)->default(0);
            $table->string('promocode')->nullable();
            $table->float('promo_amount', 8, 2)->default(0);
            $table->integer('status')->default(0)->comment = "0 = inactive, 1 = complete, 2 = confirmed, 3 = pending, 4 = cancelled, 5 = rejected";
            $table->dateTime('checkout_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bookings');
    }

}
