<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); //For User ID
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('device_token')->nullable();
            $table->string('device_type')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('status')->nullable()->default(1);
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
        Schema::dropIfExists('mobile_sessions');
    }
}
