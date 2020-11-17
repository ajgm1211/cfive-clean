<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureSelectMasterSurcharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_master_surcharge; CREATE PROCEDURE proc_master_surcharge() SELECT ms.id,sg.name,car.name as carrier,td.description as typedestiny,ct.name AS calculationtype,dr.name as direction FROM master_surcharges ms INNER JOIN surcharges sg ON ms.surcharge_id=sg.id INNER JOIN carriers car ON ms.carrier_id = car.id INNER JOIN typedestiny td ON ms.typedestiny_id=td.id INNER JOIN calculationtype ct ON ms.calculationtype_id=ct.id INNER JOIN directions dr ON ms.direction_id=dr.id;");
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
