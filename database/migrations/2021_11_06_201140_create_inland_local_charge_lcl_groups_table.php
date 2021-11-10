<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandLocalChargeLclGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_local_charge_lcl_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('automatic_inland_lcl_id')->nullable()->unsigned();
            $table->foreign('automatic_inland_lcl_id')->references('id')->on('automatic_inland_lcl_airs');
            $table->integer('local_charge_quote_lcl_id')->nullable()->unsigned();
            $table->foreign('local_charge_quote_lcl_id')->references('id')->on('local_charge_quote_lcls');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inland_local_charge_lcl_groups');
    }
}
