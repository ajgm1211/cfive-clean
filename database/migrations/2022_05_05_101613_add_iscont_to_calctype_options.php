<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIscontToCalctypeOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $calculation_types = DB::table('calculationtype')->get();

        foreach($calculation_types as $calctype){
            $options = json_decode($calctype->options,true);
            
            if(!isset($options['iscont'])){
                if(str_contains($calctype->code,'CONT') || str_contains($calctype->code,'20') || 
                    str_contains($calctype->code,'40') || str_contains($calctype->code,'45') || 
                    str_contains($calctype->code,'TEU') || str_contains($calctype->code,'TON')){
                    $options['iscont'] = true;
                }else{
                    $options['iscont'] = false;
                }
            }

            DB::table('calculationtype')
                ->where('id', $calctype->id)
                ->update(['options' => json_encode($options)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calculationtype', function (Blueprint $table) {
            //
        });
    }
}
