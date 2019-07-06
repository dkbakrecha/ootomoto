<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon_code');
            $table->integer('coupon_type')->default(1)->comment = "1 = Persentage, 2 = Amount";
            $table->integer('coupon_amount')->default(0);
            $table->integer('status')->default(1)->comment = "0 = inactive, 1 = active";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('coupon_codes');
    }

}
