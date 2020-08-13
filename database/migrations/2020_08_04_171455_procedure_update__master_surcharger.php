<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProcedureUpdateMasterSurcharger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_master_surcharge; CREATE PROCEDURE proc_master_surcharge() SELECT ms.id,sg.name,car.name as carrier, car.id as carrier_id,td.description as typedestiny,td.id as typedestiny_id, ct.name AS calculationtype, ct.id as calculationtype_id,dr.name as direction, dr.id as direction_id, gc.id as equiment_id, IFNULL(gc.name, 'ALL') AS equiment FROM master_surcharges ms INNER JOIN surcharges sg ON ms.surcharge_id=sg.id INNER JOIN carriers car ON ms.carrier_id = car.id INNER JOIN typedestiny td ON ms.typedestiny_id=td.id INNER JOIN calculationtype ct ON ms.calculationtype_id=ct.id INNER JOIN directions dr ON ms.direction_id=dr.id LEFT JOIN group_containers gc ON ms.group_container_id = gc.id;");
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
