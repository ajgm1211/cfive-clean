<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRequestLclGcEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_contract_request_lcl', function (Blueprint $table){
            $table->boolean('sentemail')->default(false)->after('time_star_one');
        });
        Schema::table('n_request_globalcharge', function (Blueprint $table){
            $table->boolean('sentemail')->default(false)->after('time_star_one');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_contract_request_lcl', function($table) {
            $table->dropColumn('sentemail');
        });
        Schema::table('n_request_globalcharge', function($table) {
            $table->dropColumn('sentemail');
        });
    }
}
