<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiCodesToOptionsInCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $carriers = DB::table('carriers')->get();

            foreach($carriers as $carrier){

                $options = json_decode($carrier->options,true);

                if($carrier->name == "CMA CGM"){
                    $options['api_code'] = "cmacgm";
                }elseif($carrier->name == "Maersk"){
                    $options['api_code'] = "maersk";
                }elseif($carrier->name == "Sealand"){
                    $options['api_code'] = "sealand";
                }elseif($carrier->name == "Evergreen"){
                    $options['api_code'] = "evergreen";
                }elseif($carrier->name == "Hapag Lloyd"){
                    $options['api_code'] = "hapag-lloyd";
                }

                $options_json = json_encode($options);

                DB::table('carriers')
                    ->where('id', $carrier->id)
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
        Schema::table('carriers', function (Blueprint $table) {
            //
        });
    }
}
