<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notification_id')->nullable()->unsigned()->comment('User ID');
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->string('service')->nullable();
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
        Schema::dropIfExists('notification_services');
    }
}
