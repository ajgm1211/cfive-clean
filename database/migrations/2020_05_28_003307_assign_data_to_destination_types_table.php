<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        \DB::table('destination_types')->insert([
            0 => [
                'id' => 1,
                'name' => 'Transhipment',
                'code' => 'transhipment',
            ],
            1 => [
                'id' => 2,
                'name' => 'Direct',
                'code' => 'direct',
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
        \DB::table('destination_types')->delete();
    }
}
