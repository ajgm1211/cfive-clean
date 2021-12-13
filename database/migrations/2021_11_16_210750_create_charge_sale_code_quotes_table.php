<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeSaleCodeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_sale_code_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('charge_id')->nullable()->unsigned();
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->integer('sale_term_code_id')->nullable()->unsigned();
            $table->foreign('sale_term_code_id')->references('id')->on('sale_term_codes');
            $table->integer('local_charge_quote_id')->nullable()->unsigned();
            $table->foreign('local_charge_quote_id')->references('id')->on('local_charge_quotes')->onDelete('cascade');
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
        Schema::dropIfExists('charge_sale_code_quotes');
    }
}
