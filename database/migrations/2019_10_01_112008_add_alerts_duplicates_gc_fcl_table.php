<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlertsDuplicatesGcFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts_duplicates_gc_fcl', function (Blueprint $table) {
            $table->Increments('id');
            $table->date('date');
            $table->integer('n_duplicate')->default(0);
            $table->integer('n_company')->default(0);
            $table->integer('status_alert_id')->unsigned();
            $table->foreign('status_alert_id')->references('id')->on('status_alerts');
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
        Schema::dropIfExists('alerts_duplicates_gc_fcl');
    }
}
