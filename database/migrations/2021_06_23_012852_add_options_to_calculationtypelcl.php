<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsToCalculationtypelcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calculationtypelcl', function (Blueprint $table) {
            $table->json('options')->after('display_name')->nullable();
        });

        $calc_types = DB::table('calculationtypelcl')->get();

        foreach($calc_types as $calc){
            $options = [];

            if(in_array($calc->id,[ 1, 2, 3, 8, 16, 18, 20, 21 ])){
                $options['type'] = 'unique'; 
            }else if(in_array($calc->id,[ 4, 11 ])){
                $options['type'] = 'chargeable'; 
            }else if(in_array($calc->id,[ 5, 6, 10, 12 ])){
                $options['type'] = 'ton'; 
            }else if(in_array($calc->id,[ 7, 13, 17, 19 ])){
                $options['type'] = 'm3'; 
            }else if(in_array($calc->id,[ 9 ])){
                $options['type'] = 'kg'; 
            }else if(in_array($calc->id,[ 14 ])){
                $options['type'] = 'package'; 
            }else if(in_array($calc->id,[ 15 ])){
                $options['type'] = 'pallet'; 
            }

            if(str_contains($calc->name,'ROUNDED')){
                $options['rounded'] = true; 
            }else{
                $options['rounded'] = false;
            }

            if(str_contains($calc->name,'TON o M3') || str_contains($calc->name,'TON O M3') ){
                $options['adaptable'] = true; 
            }else{
                $options['adaptable'] = false;
            }

            $options_json = json_encode($options);
            DB::table('calculationtypelcl')
                ->where('id', $calc->id)
                ->update(['options' => $options_json]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calculationtypelcl', function (Blueprint $table) {
            //
        });
    }
}
