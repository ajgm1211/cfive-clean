<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureFailSurchargersFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_fails_surchargers_fcl;CREATE PROCEDURE proc_fails_surchargers_fcl(IN contact_id_sc INT) SELECT * FROM `failes_surcharges` WHERE contract_id=contact_id_sc;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //CREATE PROCEDURE proc_fails_surchargers_fcl(IN contact_id_sc INT) SELECT * FROM `failes_surcharges` WHERE contract_id=contact_id_sc;
    }
}
