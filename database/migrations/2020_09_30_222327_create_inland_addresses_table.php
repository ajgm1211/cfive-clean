<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('address');
            $table->unsignedInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->unsignedInteger('port_id');
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
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
        Schema::dropIfExists('inland_addresses');
    }
}
