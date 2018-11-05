<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandsportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inlandsports', function (Blueprint $table) {
            $table->integer('port')->unsigned();
            $table->integer('inland_id')->unsigned();
            $table->foreign('port')->references('id')->on('harbors');
            $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inlandsports');
    }
}
