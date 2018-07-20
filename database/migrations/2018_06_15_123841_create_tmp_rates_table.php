<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('PortOrigin');
            $table->string('PortDestination');
            $table->string('Carrier');
            $table->string('Rate20');
            $table->string('Rate40');
            $table->string('Rate40HC');
            $table->string('Currency');
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
        Schema::dropIfExists('tmp_rates');
    }
}
