<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrigerTablePolymorphy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DELIMITER //
        CREATE TRIGGER ow_range_before_delete
        BEFORE DELETE
           ON localcharges FOR EACH ROW
        BEGIN
           delete from overweight_ranges where model_id=old.id and model_type="App\\LocalCharge";
        END; //
        DELIMITER ;');

        DB::unprepared('DELIMITER //
        CREATE TRIGGER fails_ow_range_before_delete
        BEFORE DELETE
           ON failes_surcharges FOR EACH ROW
        BEGIN
           delete from fail_overweight_ranges where model_id=old.id and model_type="App\\FailSurCharge";
        END; //
        DELIMITER ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('triger_table_polymorphy');
    }
}
