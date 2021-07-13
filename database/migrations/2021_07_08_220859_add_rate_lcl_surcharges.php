<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateLclSurcharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surcharges', function (Blueprint $table) {
           
            DB::table('surcharges')->insert([
                0 => [
                    'name' => 'Per Shipment',
                    'description' => 'LCL Rate Surcharge',
                ],
                1 => [
                    'name' => 'W/M',
                    'description' => 'LCL Rate Surcharge',
                ],
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
        //
    }
}
