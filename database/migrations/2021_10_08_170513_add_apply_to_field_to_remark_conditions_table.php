<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApplyToFieldToRemarkConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remark_conditions', function (Blueprint $table) {
            $table->enum('apply_to', ['client','internal','both'])->default('both')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_to_remark_conditions', function (Blueprint $table) {
            //
        });
    }
}
