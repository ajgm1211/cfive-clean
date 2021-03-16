<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('container_id')->unsigned();
            $table->integer('calculationtype_id')->unsigned();
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->foreign('calculationtype_id')->references('id')->on('calculationtype')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_calculations');
    }
}
