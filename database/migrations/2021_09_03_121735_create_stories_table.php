<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('story_user_id');
            $table->unsignedBigInteger('profile_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('story_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->boolean('muted')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->string('photo_link')->nullable();
            $table->string('photo_filename')->nullable();
            $table->string('text')->nullable();
            $table->unsignedBigInteger('created_at_unix')->nullable();
            $table->unsignedBigInteger('expire_in_unix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
}
