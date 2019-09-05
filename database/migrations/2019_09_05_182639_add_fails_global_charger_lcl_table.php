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
        Schema::create('fails_globalcharger_lcl', function (Blueprint $table) {
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
            $table->integer('company_user_id');
            $table->integer('account_imp_gclcl_id');
            $table->string('differentiator');
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
