<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->unique();
            $table->string('name');
            $table->string('profile_image')->nullable();
            $table->string('email')->unique();
            $table->string('phone',10)->unique();
            $table->boolean('is_phone_verified')->nullable()->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('isAdmin')->nullable();
            $table->rememberToken();
            $table->string('api_token', 60)->unique()->nullable();
            $table->boolean('auto_approve')->default(0);
            $table->integer('shop_id')->default(0);    
            $table->string('gender',1);
            $table->string('token', 10)->nullable();
            $table->integer('confirmation_alert')->default(1)->comment = "For Customer : 0 = No Confirmation, 1 = Need Booking confirmation alert";
            
            $table->integer('status')->default(0);

            // **  Provider Fields ** //
            
            $table->integer('user_type')->default(0)->comment = "0 = Service Provider, 1 = Supervisor, 2 = Customer, 3 = admin";
            $table->integer('area_id')->default(0);
            $table->string('address')->nullable();
            $table->text('map')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('lincense')->nullable();
            $table->integer('accept_payment')->default(0);
            $table->string('commission')->nullable();
            $table->integer('commission_type')->default(0)->comment = "0 = %,1 = fixed";
            $table->string('profession')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('last_login_date')->useCurrent();
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
        Schema::dropIfExists('users');
    }
}
