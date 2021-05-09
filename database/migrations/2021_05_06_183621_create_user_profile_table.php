<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('gender')->nullable();
            $table->string('birth_day')->nullable();
            $table->string('birth_month')->nullable();
            $table->string('birth_year')->nullable();
            $table->string('display_name')->nullable();
            $table->string('town')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->json('links')->nullable();
            $table->text('bio')->nullable();
            $table->string('relationship')->nullable();
            $table->json('interests')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('work_city')->nullable();
            $table->text('description')->nullable();
            $table->string('month_from')->nullable();
            $table->string('year_from')->nullable();
            $table->string('month_to')->nullable();
            $table->string('year_to')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('background_picture')->nullable();
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
        Schema::dropIfExists('user_profile');
    }
}
