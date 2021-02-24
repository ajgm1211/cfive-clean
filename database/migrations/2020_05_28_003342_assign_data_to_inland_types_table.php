<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        \DB::table('inland_types')->insert([
            0 => [
                'id' => 1,
                'name' => 'Per KM',
                'code' => 'KM',
            ],
            1 => [
                'id' => 2,
                'name' => 'Per Location',
                'code' => 'LOCATION',
            ],
        ]);
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
