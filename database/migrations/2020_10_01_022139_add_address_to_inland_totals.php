<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToInlandTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inland_totals', function (Blueprint $table) {
            $table->integer('inland_address_id')->unsigned()->nullable()->after('port_id')->onDelete('cascade');
            $table->foreign('inland_address_id')->references('id')->on('inland_addresses');

        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_inland_totals', function (Blueprint $table) {
            //
        });
    }
}
