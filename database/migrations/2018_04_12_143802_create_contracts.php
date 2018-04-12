<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('number')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('courier_id')->unsigned();
            $table->integer('origin_countrie')->unsigned();
            $table->integer('destiny_countrie')->unsigned();
            $table->date('validity');
            $table->date('expire');
            $table->enum('status',['publish','draft'])->default('draft');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('courier_id')->references('id')->on('couriers');
            $table->foreign('origin_countrie')->references('id')->on('countries');
            $table->foreign('destiny_countrie')->references('id')->on('countries');
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
        //
    }
}
