2<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('sale_term_id')->unsigned()->nullable();
            $table->foreign('sale_term_id')->references('id')->on('sale_terms')->onDelete('cascade');
            $table->integer('company_user_id')->unsigned()->nullable();
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
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
        Schema::dropIfExists('subchargers');
    }
}
