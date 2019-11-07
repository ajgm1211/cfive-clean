<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculationtypelclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('calculationtypelcl', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('display_name')->nullable();
      $table->string('code');
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
    Schema::dropIfExists('calculationtypelcl');
  }
}
