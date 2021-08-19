<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIdentifierToFollowSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_suggestions', function (Blueprint $table) {
            if (!Schema::hasColumn('follow_suggestions', 'unique_identifier')) {
                $table->string('unique_identifier')->unique();
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
        Schema::table('follow_suggestions', function (Blueprint $table) {
            //
        });
    }
}
