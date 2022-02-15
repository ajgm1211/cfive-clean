<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDestinationAmmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_ammounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge');
            $table->string('detail');
            $table->integer('units');
            $table->float('price_per_unit');
            $table->float('markup')->nullable();
            $table->integer('currency_id');
            $table->float('total_ammount');
            $table->float('total_ammount_2')->nullable();
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quotes')->onDelete('cascade');
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
        Schema::dropIfExists('destination_ammounts');
    }
}
