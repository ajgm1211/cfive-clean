<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuoteV2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_v2s', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quote_id');
            $table->string('custom_quote_id');
            $table->enum('type',['LCL','FCL']);
            $table->string('delivery_type');
            $table->json('equipment');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('price_id')->unsigned()->nullable();
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->integer('origin_port_id')->unsigned()->nullable();
            $table->foreign('origin_port_id')->references('id')->on('harbors');
            $table->integer('destination_port_id')->unsigned()->nullable();
            $table->foreign('destination_port_id')->references('id')->on('harbors');
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
            $table->date('validity_start');
            $table->date('validity_end');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
            $table->integer('incoterm_id')->unsigned();
            $table->foreign('incoterm_id')->references('id')->on('incoterms')->onDelete('cascade');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
            $table->enum('status',['Draft','Won','Sent']);
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
        Schema::dropIfExists('quote_v2s');
    }
}
