<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargeQuoteLclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_quote_lcls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge');
            $table->integer('calculation_type_id')->unsigned()->nullable();
            $table->foreign('calculation_type_id')->references('id')->on('calculationtypelcl');
            $table->integer('units');
            $table->double('price',8,2);
            $table->double('total',8,2);
            $table->integer('currency_id')->unsigned()->nullable();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->unsignedInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->unsignedInteger('port_id');
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('typedestiny');
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
        Schema::dropIfExists('local_charge_quote_lcls');
    }
}
