<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOptionsInSurcharges extends Migration
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
                
                $internal_options = [];
                if(isset($options['is_api'])){
                    $internal_options['is_api'] = $options['is_api'];

                    unset($options['is_api']);

                    $options_json = json_encode($options);
                    $internal_options_json = json_encode($internal_options);

                    DB::table('surcharges')
                        ->where('id', $surcharge->id)
                        ->update([
                            'options' => $options_json,
                            'internal_options' => $internal_options_json
                        ]);
                }
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
