<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurchargersForCompanyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surchargers_for_company_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('company_auto_id')->unsigned();
            $table->foreign('company_auto_id')->references('id')->on('companies_auto_importations')->onDelete('cascade');
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
        Schema::dropIfExists('surchargers_for_company_user');
    }
}
