<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRequestLclGcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_contract_request_lcl', function (Blueprint $table){
            $table->string('time_star')->nullable()->after('updated');
            $table->string('time_total')->nullable()->after('time_star');
            $table->boolean('time_star_one')->after('data')->default(false);
        });
        
        Schema::table('n_request_globalcharge', function (Blueprint $table){
            $table->string('time_star')->nullable()->after('updated');
            $table->string('time_total')->nullable()->after('time_star');
            $table->boolean('time_star_one')->after('data')->default(false);
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
            $table->dropColumn('time_star');
            $table->dropColumn('time_total');
            $table->dropColumn('time_star_one');
        });
        
        Schema::table('n_request_globalcharge', function($table) {
            $table->dropColumn('time_star');
            $table->dropColumn('time_total');
            $table->dropColumn('time_star_one');
        });
    }
}
