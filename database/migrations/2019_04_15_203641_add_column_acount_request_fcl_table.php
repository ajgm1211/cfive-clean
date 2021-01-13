<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAcountRequestFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_import_cfcl', function (Blueprint $table) {
            $table->integer('request_id')->nullable()->after('company_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_import_cfcl', function ($table) {
            $table->dropColumn('request_id');
        });
    }
}
