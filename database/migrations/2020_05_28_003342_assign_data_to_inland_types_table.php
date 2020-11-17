<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssignDataToInlandTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('inland_types')->delete();

        \DB::table('inland_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Per KM',
                'code' => 'KM',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Per Location',
                'code' => 'LOCATION',
            )
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('inland_types')->delete();
    }
}
