<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransitTimeFailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transit_time_fails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin')->nullable();
            $table->string('destiny')->nullable();
            $table->string('carrier')->nullable();
            $table->string('destination_type')->nullable();
            $table->string('transit_time')->nullable();
            $table->string('via')->nullable();
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
        Schema::dropIfExists('transit_time_fails');
    }
}
