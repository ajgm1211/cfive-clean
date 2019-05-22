<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandsdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inlandsdetails', function (Blueprint $table) {
               $table->increments('id');
            $table->string('lower');
            $table->string('upper');
            $table->double('ammount');
            $table->string('type');
            $table->integer('currency_id')->unsigned();
            $table->integer('inland_id')->unsigned();
            $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inlandsdetails');
    }
}
