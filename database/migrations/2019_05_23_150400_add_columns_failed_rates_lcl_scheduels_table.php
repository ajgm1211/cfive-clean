<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsFailedRatesLclScheduelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('failes_rate_lcl', function(Blueprint $table){
            $table->string('schedule_type')->nullable()->after('currency_id');
            $table->string('transit_time')->nullable()->after('schedule_type');
            $table->string('via')->nullable()->after('transit_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('failes_rate_lcl', function($table) {
            $table->dropColumn('schedule_type');
            $table->dropColumn('transit_time');
            $table->dropColumn('via');
        });
    }
}
