<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termsport', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port_id')->unsigned();
            $table->integer('term_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('termsAndConditions')->onDelete('cascade');
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
        Schema::dropIfExists('termsport');
    }
}
