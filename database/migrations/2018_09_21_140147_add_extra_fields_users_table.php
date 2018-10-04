<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsUsersTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dateTime('last_login')->nullable()->after('company_user_id');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::table('users', function($table) {
      $table->dropColumn('last_login');

    });
  }
}
