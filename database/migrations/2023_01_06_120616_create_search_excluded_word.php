<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchExcludedWord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_excluded_word', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('search_id')->nullable()->unsigned()->comment('User ID');
            $table->foreign('search_id')->references('id')->on('searchs')->onDelete('cascade');
            $table->string('excluded_word');
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
        Schema::dropIfExists('search_excluded_word');
    }
}
