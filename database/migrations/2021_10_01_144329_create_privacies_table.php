<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privacies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('setting_id');
            $table->unsignedBigInteger('blocked_user_id');
            $table->unsignedBigInteger('blocked_by_user_id');
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('stat_id');

            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('blocked_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blocked_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('stat_id')->references('id')->on('stats')->onDelete('cascade');

            $table->boolean('blocked_profile')->default(0);
            $table->boolean('blocked_messages')->default(0);
            $table->boolean('blocked_stories')->default(0);
            $table->string('blocked_profile_duration')->nullable();
            $table->string('blocked_messages_duration')->nullable();
            $table->string('blocked_stories_duration')->nullable();
            $table->unsignedBigInteger('created_in_unix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privacies');
    }
}
