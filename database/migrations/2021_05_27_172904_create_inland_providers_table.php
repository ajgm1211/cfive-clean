<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider_type');
            $table->integer('provider_id');
            $table->integer('automatic_inland_id')->unsigned();
            $table->foreign('automatic_inland_id')
            ->references('id')->on('automatic_inlands');
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
        Schema::dropIfExists('inland_providers');
    }
}
