<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('globalcharport', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port')->unsigned();
            $table->integer('globalcharge_id')->unsigned();
            $table->foreign('port')->references('id')->on('harbors');
            $table->foreign('globalcharge_id')->references('id')->on('globalcharges')->onDelete('cascade');
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
        Schema::dropIfExists('globalcharport');
    }
}
