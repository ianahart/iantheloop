<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_suggestions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('prospect_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->mediumInteger('mutual_follows')->default(0);
            $table->boolean('rejected')->default(0);
            $table->unsignedBigInteger('rejected_at')->nullable();
            $table->string('unique_identifier')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_suggestions');
    }
}
