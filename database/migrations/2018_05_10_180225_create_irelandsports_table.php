<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIrelandsportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('irelandsports', function (Blueprint $table) {
            $table->integer('port')->unsigned();
            $table->integer('ireland_id')->unsigned();
            $table->foreign('port')->references('id')->on('harbors');
            $table->foreign('ireland_id')->references('id')->on('irelands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('irelandsports');
    }
}
