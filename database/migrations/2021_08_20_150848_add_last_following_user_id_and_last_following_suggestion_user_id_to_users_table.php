<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastFollowingUserIdAndLastFollowingSuggestionUserIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_following_user_id')) {
                $table->mediumInteger('last_following_user_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_following_suggestion_user_id')) {
                $table->mediumInteger('last_following_suggestion_user_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
