<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationQuoteStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_quote_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('integration_quote_statuses');
    }
}
