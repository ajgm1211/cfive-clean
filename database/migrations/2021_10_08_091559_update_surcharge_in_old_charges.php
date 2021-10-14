<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSurchargeInOldCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charges', function (Blueprint $table) {
            $ocean_surcharge = DB::table('surcharges')->where([['name','Ocean Freight'],['company_user_id',null]])->first();

            if(is_null($ocean_surcharge)){
                DB::table('surcharges')
                    ->insert([
                        "name" => "Ocean Freight", 
                        "description"=>"Rate Surcharge", 
                        "options" => json_encode(['is_api'=>'false']),
                    ]);

                $ocean_surcharge = DB::table('surcharges')->where([['name','Ocean Freight'],['company_user_id',null]])->first();
            }

            $freight_charges = DB::table('charges')->where('surcharge_id',null)->get();

            foreach($freight_charges as $charge){
                DB::table('charges')
                    ->where('id', $charge->id)
                    ->update(['surcharge_id' => $ocean_surcharge->id]);
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
        Schema::table('charges', function (Blueprint $table) {
            //
        });
    }
}
