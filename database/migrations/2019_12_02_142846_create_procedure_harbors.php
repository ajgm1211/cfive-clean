<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureHarbors extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    //
    DB::unprepared("DROP PROCEDURE IF EXISTS proc_harbors;CREATE PROCEDURE proc_harbors()  SELECT har.id , har.name , har.code, har.display_name, har.coordinates , har.varation , coun.name as country_id FROM harbors as har inner join countries coun on har.country_id = coun.id;");

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
