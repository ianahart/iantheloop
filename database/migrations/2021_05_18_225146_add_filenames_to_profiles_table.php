<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilenamesToProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            Schema::table('profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('profiles', 'profile_filename')) {
                    $table->string('profile_filename')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'background_filename')) {
                    $table->string('background_filename')->nullable();
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            //
        });
    }
}
