<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationAndAddBackgroundAndAddFontSizeToStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stories', function (Blueprint $table) {
            if (!Schema::hasColumn('stories', 'duration')) {
                $table->integer('duration')->nullable();
            }
            if (!Schema::hasColumn('stories', 'background')) {
                $table->mediumText('background')->nullable();
            }
            if (!Schema::hasColumn('stories', 'font_size')) {
                $table->string('font_size')->nullable();
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
        Schema::table('stories', function (Blueprint $table) {
            //
        });
    }
}
