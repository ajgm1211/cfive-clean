<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFailsGlobalChargerLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_globalcharger_lcl', function (Blueprint $table) {
            $table->increments('id');
            $table->string('surcharge');
            $table->string('origin');
            $table->string('destiny');
            $table->string('typedestiny');
            $table->string('calculationtypelcl');
            $table->string('ammount');
            $table->string('minimum');
            $table->string('validity');
            $table->string('expire');
            $table->string('currency');
            $table->boolean('port')->default(true);
            $table->boolean('country')->default(false);
            $table->string('carrier');
            $table->integer('company_user_id')->unsigned();
            $table->integer('account_imp_gclcl_id')->unsigned();
            $table->integer('differentiator');
            $table->foreign('account_imp_gclcl_id')->references('id')->on('account_importation_global_charger_lcls')->onDelete('cascade');
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
        Schema::dropIfExists('fails_globalcharger_lcl');
    }
}
