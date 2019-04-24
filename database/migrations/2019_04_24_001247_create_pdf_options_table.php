<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdf_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->enum('show_type',['detailed','total in']);
            $table->enum('total_in_currency',['USD','EUR']);
            $table->enum('origin_charges_currency',['USD','EUR'])->nullable();
            $table->enum('destination_charges_currency',['USD','EUR'])->nullable();
            $table->enum('language',['English','Portuguese','Spanish']);
            $table->boolean('show_carrier')->nullable();
            $table->boolean('show_logo')->nullable();
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
        Schema::dropIfExists('pdf_options');
    }
}
