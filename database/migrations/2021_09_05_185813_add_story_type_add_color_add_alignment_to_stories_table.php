<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoryTypeAddColorAddAlignmentToStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stories', function (Blueprint $table) {
            if (!Schema::hasColumn('stories', 'story_type')) {
                $table->string('story_type')->nullable();
            }
            if (!Schema::hasColumn('stories', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('stories', 'alignment')) {
                $table->string('alignment')->nullable();
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
