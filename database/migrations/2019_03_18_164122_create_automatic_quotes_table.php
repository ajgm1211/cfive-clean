<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incoterm_id')->unsigned();
            $table->foreign('incoterm_id')->references('id')->on('incoterms')->onDelete('cascade');
            $table->integer('type');
            $table->string('delivery_type');
            $table->json('equipment');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->integer('price_level_id')->unsigned();
            $table->foreign('price_level_id')->references('id')->on('price_levels')->onDelete('cascade');
            $table->date('validity_start');
            $table->date('validity_end');
            $table->integer('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automatic_quotes');
    }
}
