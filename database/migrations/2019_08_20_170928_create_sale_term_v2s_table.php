<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTermV2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_term_v2s', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->integer('port_id')->unsigned()->nullable();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->integer('airport_id')->unsigned()->nullable();
            $table->foreign('airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->enum('type',['Origin','Destination']);
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
        Schema::dropIfExists('sale_term_v2s');
    }
}
