<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyUserQuoteSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_user_quote_segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id');
            $table->unsignedInteger('company_user_id');
            $table->unsignedInteger('quote_segment_type_id');
            $table->timestamps();
            
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
            $table->foreign('quote_segment_type_id')->references('id')->on('quote_segment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user_quote_segments');
    }
}