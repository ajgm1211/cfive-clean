<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContainerCalculationForPerTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        $containers = DB::table('containers')->get();
        $per_tracking = DB::table('calculationtype')->where('name','Per Tracking')->first();

        foreach($containers as $container){
            DB::table('container_calculations')->insert(
                [
                    'container_id' => $container->id,
                    'calculationtype_id' => $per_tracking->id,
                ]
            );
        }

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
