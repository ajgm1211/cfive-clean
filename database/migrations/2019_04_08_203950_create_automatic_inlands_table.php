<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticInlandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_inlands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->string('provider');
            $table->string('contract');
            $table->integer('port_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->enum('type',['Origin','Destination']);
            $table->float('distance');
            $table->json('rate');
            $table->json('markup');
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
        Schema::dropIfExists('automatic_inlands');
    }
}
