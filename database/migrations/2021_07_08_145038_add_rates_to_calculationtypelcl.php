<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatesToCalculationtypelcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calculationtypelcl', function (Blueprint $table) {

            $options = [
                "type" => "rate_only",
                "rounded" => false,
                "adaptable" => false,
            ];

            $json_options = json_encode($options);
            
            DB::table('calculationtypelcl')->insert(
                array(
                    'name' => 'RATE',
                    'code' => 'RATE',
                    'display_name' => 'RATE',
                    'options' => $json_options,
                )
            );
        });
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
