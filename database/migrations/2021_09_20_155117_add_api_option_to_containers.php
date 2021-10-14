<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiOptionToContainers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('containers', function (Blueprint $table) {
            $containers = DB::table('containers')->get();

            foreach($containers as $container){

                $options = json_decode($container->options,true);
                
                if(in_array($container->code, ['20DV','40DV','40HC','20RF','40RF'])){
                    $options['has_api'] = true;
                }else{
                    $options['has_api'] = false;
                }

                $options_json = json_encode($options);

                DB::table('containers')
                    ->where('id', $container->id)
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
        Schema::table('containers', function (Blueprint $table) {
            //
        });
    }
}
