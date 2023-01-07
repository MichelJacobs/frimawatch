<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationExcludedService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_excluded_service', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notification_id')->nullable()->unsigned()->comment('User ID');
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->string('excluded_service');
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
        Schema::dropIfExists('notification_excluded_service');
    }
}
