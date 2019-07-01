<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRatesLclScheduelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rates_lcl', function(Blueprint $table){
            $table->integer('schedule_type_id')->unsigned()->nullable()->after('currency_id');
            $table->integer('transit_time')->unsigned()->nullable()->after('schedule_type_id');
            $table->string('via')->nullable()->after('transit_time');
            $table->foreign('schedule_type_id')->references('id')->on('schedule_type')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates_lcl', function($table) {
            $table->dropColumn('schedule_type_id');
            $table->dropColumn('transit_time');
            $table->dropColumn('via');
        });

    }
}
