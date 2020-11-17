<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureFailRatesFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_fail_rates_fcl;CREATE PROCEDURE proc_fail_rates_fcl(In contract_id_sc INT) SELECT * FROM failes_rates WHERE contract_id = contract_id_sc;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //CREATE PROCEDURE fail_rates_fcl(In contract_id INT) SELECT * FROM failes_rates WHERE contract_id = contract_id;
    }
}
