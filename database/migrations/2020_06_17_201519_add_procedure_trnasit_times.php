<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureTrnasitTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS proc_transit_times; CREATE PROCEDURE proc_transit_times() SELECT tt.id,hbori.name as origin,hbdes.name as destiny,carr.name AS carrier, sht.name as destination_type,transit_time,via FROM transit_times tt INNER JOIN harbors hbori ON tt.origin_id = hbori.id INNER JOIN harbors hbdes ON tt.destination_id = hbdes.id INNER JOIN carriers carr ON tt.carrier_id = carr.id INNER JOIN schedule_type sht ON tt.service_id = sht.id;');
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
