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
            
            DB::table('calculationtypelcl')->insert([
                'name' => 'W/M',
                'code' => 'W/M',
                'display_name' => 'W/M',
                'options' => $json_options
            ]);
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
