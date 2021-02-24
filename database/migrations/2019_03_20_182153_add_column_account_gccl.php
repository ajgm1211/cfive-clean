<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAccountGccl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_importation_globalcharge', function (Blueprint $table) {
            $table->string('namefile')->nullable()->after('company_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_importation_globalcharge', function ($table) {
            $table->dropColumn('namefile');
        });
    }
}
