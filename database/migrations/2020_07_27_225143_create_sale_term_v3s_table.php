<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTermV3sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_term_v3s', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('port_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('sale_term_types')->onDelete('cascade'); 
            $table->integer('company_user_id')->unsigned();
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
        Schema::dropIfExists('sale_term_v3s');
    }
}
