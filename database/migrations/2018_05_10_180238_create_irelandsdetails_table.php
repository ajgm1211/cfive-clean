<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIrelandsdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('irelandsdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lower');
            $table->string('upper');
            $table->double('ammount');
            $table->string('type');
            $table->integer('currency_id')->unsigned();
            $table->integer('ireland_id')->unsigned();
            $table->foreign('ireland_id')->references('id')->on('irelands')->onDelete('cascade');
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
        Schema::dropIfExists('irelandsdetails');
    }
}
