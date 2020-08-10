<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CalculationTypesContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculation_types_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('calculationtype_base_id')->unsigned();
            $table->integer('calculationtype_content_id')->unsigned();
            $table->foreign('calculationtype_base_id')->references('id')->on('calculationtype');
            $table->foreign('calculationtype_content_id')->references('id')->on('calculationtype');
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
        Schema::dropIfExists('calculation_types_contents');
    }
}
