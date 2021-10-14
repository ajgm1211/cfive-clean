<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsToApiSurcharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surcharges', function (Blueprint $table) {
            $surcharges = DB::table('surcharges')->get();

            foreach($surcharges as $surcharge){

                $options = json_decode($surcharge->options,true);
                
                if($surcharge->description == 'from API'){
                    $options['is_api'] = true;
                }else{
                    $options['is_api'] = false;
                }

                $options_json = json_encode($options);

                DB::table('surcharges')
                    ->where('id', $surcharge->id)
                    ->update(['options' => $options_json]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surcharges', function (Blueprint $table) {
            //
        });
    }
}
