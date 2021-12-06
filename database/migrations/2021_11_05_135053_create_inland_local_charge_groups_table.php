<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandLocalChargeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_local_charge_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('automatic_inland_id')->nullable()->unsigned();
            $table->foreign('automatic_inland_id')->references('id')->on('automatic_inlands');
            $table->integer('local_charge_quote_id')->nullable()->unsigned();
            $table->foreign('local_charge_quote_id')->references('id')->on('local_charge_quotes');
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
        Schema::dropIfExists('inland_local_charge_groups');
    }
}
