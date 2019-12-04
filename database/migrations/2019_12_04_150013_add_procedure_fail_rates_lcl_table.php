<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureFailRatesLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_fails_rates_lcl;CREATE PROCEDURE proc_fails_rates_lcl(IN contractlcl_id_sc INT) SELECT * FROM `failes_rate_lcl` WHERE contractlcl_id = contractlcl_id_sc;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //CREATE PROCEDURE proc_fails_rates_lcl(IN contractlcl_id_sc INT) SELECT * FROM `failes_rate_lcl` WHERE contractlcl_id = contractlcl_id_sc;
    }
}
