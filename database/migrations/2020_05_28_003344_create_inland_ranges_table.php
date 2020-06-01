<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('gp_container_id')->nullable()->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('inland_id')->unsigned();
            $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
            $table->foreign('gp_container_id')->references('id')->on('group_containers');
            $table->foreign('currency_id')->references('id')->on('currency');
           
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
