<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountImportationGlobalChargerLclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_importation_global_charger_lcls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('date');
            $table->string('namefile')->nullable();
            $table->integer('company_user_id')->unsigned();
            $table->integer('requestgclcl_id')->nullable()->unsigned();
            $table->enum('status',['complete','incomplete'])->default('incomplete');
            $table->foreign('requestgclcl_id')->references('id')->on('new_request_global_charger_lcls');
            $table->foreign('company_user_id')->references('id')->on('company_users');
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
        Schema::dropIfExists('account_importation_global_charger_lcls');
    }
}
