<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_user_id');
            $table->unsignedBigInteger('author_user_id');
            $table->foreign('subject_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('author_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('post_text')->nullable();
            $table->string('photo_filename')->nullable();
            $table->string('video_filename')->nullable();
            $table->string('photo_link')->nullable();
            $table->string('video_link')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('comments_count')->default(0);
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
        Schema::dropIfExists('posts');
    }
}
