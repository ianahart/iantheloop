<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRememberMeAddLookupAddValidatorAddIpAddressAddUserAgentAddExpireInAddNonceToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'remember_me')) {
                $table->boolean('remember_me')->default(0);
            }
            if (!Schema::hasColumn('settings', 'lookup')) {
                $table->binary('lookup', 32)->nullable();
            }
            if (!Schema::hasColumn('settings', 'validator')) {
                $table->binary('validator', 32)->nullable();
            }
            if (!Schema::hasColumn('settings', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('settings', 'user_agent')) {
                $table->string('user_agent')->nullable();
            }
            if (!Schema::hasColumn('settings', 'expire_in')) {
                $table->mediumInteger('expire_in')->nullable();
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
            $table->dropColumn('remember_me', 'lookup', 'validator', 'ip_address', 'user_agent', 'expire_in');
        });
    }
}
