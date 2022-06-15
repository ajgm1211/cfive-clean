<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValuesToOptionsFieldsInChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $charges = DB::table('charges')->get();

        foreach($charges as $charge){

            $options = json_encode([
                "selected" => false,
                "show_as"=> null 
            ]);

            DB::table('charges')
                ->where('id', $charge->id)
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
        Schema::table('options_fields_in_charges', function (Blueprint $table) {
            //
        });
    }
}
