<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandKmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_kms', function (Blueprint $table) {
            $table->increments('id');
            $table->json('json_containers');
            $table->integer('inland_id')->unsigned();
            $table->integer('currency_id')->unsigned();

            $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('inland_kms');
    }
}
