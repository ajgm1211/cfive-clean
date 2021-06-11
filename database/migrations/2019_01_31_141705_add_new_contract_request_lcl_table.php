<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewContractRequestLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_contract_request_lcl', function (Blueprint $table) {
            $table->increments('id');
            $table->string('namecontract');
            $table->string('numbercontract');
            $table->string('validation');
            $table->integer('company_user_id')->unsigned();
            $table->string('namefile');
            $table->enum('status', ['Pending', 'Processing', 'Done'])->default('Pending');
            $table->integer('user_id')->unsigned();
            $table->dateTime('created');
            $table->dateTime('updated')->nullable();
            $table->string('username_load')->default('Not assigned');
            $table->json('type');
            $table->json('data');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('new_contract_request_lcl');
    }
}
