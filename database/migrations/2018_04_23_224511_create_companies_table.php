<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('business_name');
            $table->string('phone');
            $table->string('address',200);
            $table->string('email');
            $table->integer('associated_quotes')->nullable();
            $table->integer('company_user_id')->unsigned();
            //$table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
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
        Schema::dropIfExists('companies');
    }
}
