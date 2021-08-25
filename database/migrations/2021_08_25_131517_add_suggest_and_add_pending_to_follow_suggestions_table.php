<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuggestAndAddPendingToFollowSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('follow_suggestions', function (Blueprint $table) {
            if (!Schema::hasColumn('follow_suggestions', 'suggest')) {
                $table->boolean('suggest')->default(1);
            }
            if (!Schema::hasColumn('follow_suggestions', 'pending')) {
                $table->boolean('pending')->default(0);
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
