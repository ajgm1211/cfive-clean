<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticInlandTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_inland_totals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quote_v2s');
            $table->unsignedInteger('port_id');
            $table->foreign('port_id')->references('id')->on('harbors');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->enum('type',['Origin','Destination']);
            $table->json('totals')->nullable();
            $table->json('markups')->nullable();
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
        Schema::dropIfExists('automatic_inland_totals');
    }
}
