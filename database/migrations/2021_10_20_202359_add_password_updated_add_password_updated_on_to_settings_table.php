<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordUpdatedAddPasswordUpdatedOnToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'password_updated')) {
                $table->boolean('password_updated')->default(0);
            }
            if (!Schema::hasColumn('settings', 'password_updated_on')) {
                $table->unsignedBigInteger('password_updated_on')->nullable();
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('password_updated', 'password_updated_on');
        });
    }
}
