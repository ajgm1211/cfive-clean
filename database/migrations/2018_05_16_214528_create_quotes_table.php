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
            $table->string('incoterm');
            $table->date('validity')->nullable();
            $table->date('pick_up_date');
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('origin_harbor_id')->unsigned()->nullable();
            $table->foreign('origin_harbor_id')->references('id')->on('harbors');
            $table->integer('destination_harbor_id')->unsigned()->nullable();
            $table->foreign('destination_harbor_id')->references('id')->on('harbors');
            $table->integer('status_id')->unsigned()->nullable()->default(1);
            $table->integer('price_id')->unsigned()->nullable();
            $table->foreign('price_id')->references('id')->on('prices');
            $table->integer('type')->unsigned();
            $table->string('qty_20')->nullable();
            $table->string('qty_40')->nullable();
            $table->string('qty_40_hc')->nullable();
            $table->string('delivery_type')->nullable();
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
