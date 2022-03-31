<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultValueInOptionsFieldsToProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $providers = DB::table('providers')->get();

        foreach($providers as $provider){

            $options = json_encode([
                "generic" => false
            ]);

            DB::table('providers')
                ->where('id', $provider->id)
                ->update(['options' => $options]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            //
        });
    }
}
