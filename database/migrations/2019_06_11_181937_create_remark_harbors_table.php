<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemarkHarborsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('remark_harbors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port_id')->unsigned();
            $table->integer('remark_condition_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->foreign('remark_condition_id')->references('id')->on('remark_conditions')->onDelete('cascade');
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
    Schema::dropIfExists('remark_harbors');
  }
}
