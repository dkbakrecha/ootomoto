<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle', function (Blueprint $table) {
            $table->increments('id');                
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('vehicle_class')->default(0)->comment="0 = Two , 1  = Four";
            $table->string('reg_no');
            $table->string('mfg_year');
            $table->integer('macker_id');
            $table->integer('model_id');
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
        Schema::dropIfExists('vehicle');
    }
}
