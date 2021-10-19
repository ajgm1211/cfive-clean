<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotLocalChargeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_local_charge_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('charge_id')->unsigned();
            $table->integer('local_charge_quote_id')->unsigned();
            $table->integer('quote_id')->unsigned();
            $table->foreign('charge_id')
                ->references('id')->on('charges')
                ->onDelete('cascade');
            $table->foreign('local_charge_quote_id')
                    ->references('id')->on('local_charge_quotes')
                    ->onDelete('cascade');
            $table->foreign('quote_id')
                            ->references('id')->on('quote_v2s')
                            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_local_charge_quotes');
    }
}
