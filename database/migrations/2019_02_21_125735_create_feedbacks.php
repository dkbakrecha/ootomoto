<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacks extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_id')->unsigned(); //For User ID
            $table->integer('to_id')->unsigned(); //For User ID
            $table->text('message');
            $table->integer('message_type')->default(0)->comment = "0 = text, 1 = image";
            $table->integer('status_to_id')->default(0);
            $table->integer('status_from_id')->default(1);
            $table->integer('status')->default(1)->comment = "0 = inactive, 1 = active, 2 = deleted";
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('feedbacks');
    }

}
