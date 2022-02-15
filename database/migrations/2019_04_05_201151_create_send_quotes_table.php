<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('to');
            $table->string('from');
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->integer('status');
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
        Schema::dropIfExists('send_quotes');
    }
}
