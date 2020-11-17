<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssignDataToDestinationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('destination_types')->delete();

        \DB::table('destination_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Transhipment',
                'code' => 'transhipment',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Direct',
                'code' => 'direct',
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
        \DB::table('destination_types')->delete();
    }
}
