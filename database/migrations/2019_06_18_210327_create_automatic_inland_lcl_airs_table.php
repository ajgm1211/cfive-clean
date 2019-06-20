<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticInlandLclAirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_inland_lcl_airs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->integer('automatic_rate_id')->unsigned();
            $table->foreign('automatic_rate_id')->references('id')->on('automatic_rates')->onDelete('cascade');
            $table->string('provider')->nullable();
            $table->string('contract')->nullable();
            $table->integer('port_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->enum('type',['Origin','Destination']);
            $table->float('distance')->nullable();
            $table->float('units')->nullable();
            $table->float('price_per_unit')->nullable();
            $table->float('markup')->nullable();
            $table->float('total')->nullable();
            $table->date('validity_start');
            $table->date('validity_end');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
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
        Schema::dropIfExists('automatic_inland_lcl_airs');
    }
}
