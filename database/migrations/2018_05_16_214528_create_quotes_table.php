<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users');
            $table->string('incoterm');
            $table->date('validity')->nullable();
            $table->integer('modality');
            $table->date('pick_up_date');
            $table->integer('type_cargo')->nullable();
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('origin_harbor_id')->unsigned()->nullable();
            $table->foreign('origin_harbor_id')->references('id')->on('harbors');
            $table->integer('destination_harbor_id')->unsigned()->nullable();
            $table->foreign('destination_harbor_id')->references('id')->on('harbors');
            $table->integer('origin_airport_id')->unsigned()->nullable();
            $table->foreign('origin_airport_id')->references('id')->on('airports');
            $table->integer('destination_airport_id')->unsigned()->nullable();
            $table->foreign('destination_airport_id')->references('id')->on('airports');
            $table->integer('price_id')->unsigned()->nullable();
            $table->foreign('price_id')->references('id')->on('prices');
            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('carrier_id')->unsigned()->nullable();
            $table->foreign('carrier_id')->references('id')->on('carriers');
            $table->integer('airline_id')->unsigned()->nullable();
            $table->foreign('airline_id')->references('id')->on('airlines');              
            $table->integer('type')->unsigned();
            $table->string('qty_20')->nullable();
            $table->string('qty_40')->nullable();
            $table->string('qty_40_hc')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->float('total_weight')->nullable();
            $table->float('total_volume')->nullable();
            $table->string('delivery_type')->nullable();
            $table->float('sub_total_origin')->nullable();
            $table->float('sub_total_freight')->nullable();
            $table->float('sub_total_destination')->nullable();
            $table->float('total_markup_origin')->nullable();
            $table->float('total_markup_freight')->nullable();
            $table->float('total_markup_destination')->nullable();            
            $table->integer('status_quote_id')->unsigned()->default(1);
            $table->foreign('status_quote_id')->references('id')->on('status_quotes');
            $table->integer('sale_term_id')->unsigned()->nullable();
            $table->foreign('sale_term_id')->references('id')->on('sale_terms');            
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
        Schema::dropIfExists('quotes');
    }
}