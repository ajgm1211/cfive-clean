<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRequestFclTTGTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newcontractrequests',function (Blueprint $table) {
            $table->string('time_manager')->nullable()->after('time_total');
        });
        
        Schema::table('new_contract_request_lcl',function (Blueprint $table) {
            $table->string('time_manager')->nullable()->after('time_total');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
