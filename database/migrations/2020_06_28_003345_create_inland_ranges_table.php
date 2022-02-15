<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInlandRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('inland_ranges');
        Schema::enableForeignKeyConstraints();

        Schema::create('inland_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lower');
            $table->string('upper');
            $table->json('json_containers');
            $table->integer('currency_id')->unsigned();
            $table->integer('inland_id')->unsigned();
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
        Schema::dropIfExists('inland_ranges');
    }
}
