<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldSearhTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {

    Schema::table('search_rates', function (Blueprint $table) {
      $table->json('equipment')->nullable()->after('pick_up_date');
      $table->string('delivery')->nullable()->after('equipment');
      $table->string('direction')->nullable()->after('delivery');
      $table->string('incoterm')->nullable()->after('direction');
      $table->enum('type', ['LCL', 'FCL'])->nullable()->after('incoterm');;
      $table->integer('incoterm_id')->unsigned()->nullable()->after('type');
      $table->integer('company_user_id')->unsigned()->nullable()->after('incoterm_id');
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
      $table->foreign('incoterm_id')->references('id')->on('incoterms')->onDelete('cascade');

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
