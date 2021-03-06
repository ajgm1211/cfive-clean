<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('custom_quote_id')->nullable();
            $table->enum('type', ['LCL', 'FCL', 'AIR']);
            $table->string('delivery_type');
            $table->json('equipment')->nullable();
            //$table->integer('cargo_type')->nullable();
            $table->float('chargeable_weight')->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('price_id')->unsigned()->nullable();
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->date('validity_start');
            $table->date('validity_end');
            $table->date('date_issued');
            $table->integer('incoterm_id')->unsigned()->nullable();
            $table->foreign('incoterm_id')->references('id')->on('incoterms')->onDelete('cascade');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
            $table->enum('status', ['Draft', 'Won', 'Sent']);
            $table->text('payment_conditions')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
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
