<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNamesOnApiProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('api_providers')
            ->where('id',1)
            ->update([
                "name" => "CMA RATES"
        ]);

        DB::table('api_providers')
            ->where('id',2)
            ->update([
                "name" => "MAERSK SPOT"
        ]);
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
