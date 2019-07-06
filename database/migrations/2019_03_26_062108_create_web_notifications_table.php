<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_notifications', function (Blueprint $table) {
            //
            $table->increments('id');

            $table->integer('notification_for')
                    ->comment = "0 = Admin, 1 = Service Provider, 2 = Supervisor";

            $table->integer('user_id')
                    ->comment = "User who performed the event for notification";

            $table->integer('event_type')
                ->comment = "0 = New Service Provider Registered, 1 = Need to accept or reject booking, 2 = Review or rating given about shop, 3 = Flag bad review to admin";

            $table->integer('notification_for')
                    ->change()
                    ->comment = "User id of the notification receiver";

            // $table->enum('event_type', [
            //     'New Service Provider Registered',
            //     'Need to accept or reject booking',
            //     'Review or rating given about shop'
            // ]);

            $table->text('event');

            $table->boolean('is_read')->default(0);

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
        Schema::drop('web_notifications');
    }
}
