<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticRateTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_rate_totals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quote_v2s');
            $table->unsignedInteger('automatic_rate_id');
            $table->foreign('automatic_rate_id')->references('id')->on('automatic_rates')->onDelete('cascade');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('automatic_rate_totals');
    }
}
