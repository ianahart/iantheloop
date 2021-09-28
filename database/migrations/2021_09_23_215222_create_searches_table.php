<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('searcher_user_id');
            $table->unsignedBigInteger('searched_user_id');
            $table->unsignedBigInteger('profile_id');
            $table->foreign('searcher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('searched_user_id')->references('id')->on('users')->oneDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->string('search_value')->nullable();
            $table->string('formatted_search_value')->nullable();
            $table->unsignedBigInteger('created_in_unix')->nullable();
            $table->unsignedBigInteger('purge_in_unix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
