<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageNamesToApiProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_providers', function (Blueprint $table) {
            //
        });

        DB::table('api_providers')
            ->where('id',1)
            ->update([
                "image" => "cma.png"
        ]);

        DB::table('api_providers')
            ->where('id',2)
            ->update([
                "image" => "maersk.png"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_providers', function (Blueprint $table) {
            //
        });
    }
}
