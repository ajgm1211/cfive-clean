<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewRequestGlobalChargerLclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_request_global_charger_lcls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('validation');
            $table->integer('company_user_id')->unsigned();
            $table->string('namefile');
            $table->enum('status',['Pending', 'Processing', 'Done', 'Review'])->default('Pending');
            $table->integer('user_id')->unsigned();
            $table->dateTime('created');
            $table->dateTime('updated')->nullable();
            $table->string('time_start')->nullable();
            $table->string('time_total')->nullable();
            $table->string('username_load')->default('Not assigned');
            $table->boolean('time_star_one')->default(false);
            $table->string('sentemail')->default(false);
            $table->foreign('company_user_id')->references('id')->on('company_users');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('new_request_global_charger_lcls');
    }
}
