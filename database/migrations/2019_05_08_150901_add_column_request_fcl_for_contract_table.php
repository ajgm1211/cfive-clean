<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRequestFclForContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newcontractrequests', function (Blueprint $table) {
            $table->integer('contract_id')->unsigned()->nullable()->after('sentemail');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newcontractrequests', function ($table) {
            $table->dropColumn('contract_id');
        });
    }
}
