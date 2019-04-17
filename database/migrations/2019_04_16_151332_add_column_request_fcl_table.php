<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRequestFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newcontractrequests', function (Blueprint $table){
            $table->integer('direction_id')->nullable()->after('validation')->unsigned();
            $table->foreign('direction_id')->references('id')->on('directions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newcontractrequests', function($table) {
            $table->dropColumn('direction');
        });
    }
}
