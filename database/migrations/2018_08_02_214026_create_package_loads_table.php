<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_loads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_cargo');
            $table->integer('quantity');
            $table->float('height');
            $table->float('width');
            $table->float('large');
            $table->float('weight');
            $table->float('volume');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quotes')->onDelete('cascade');            
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
        Schema::dropIfExists('package_loads');
    }
}
