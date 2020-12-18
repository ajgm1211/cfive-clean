<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPdfTemplateFieldToCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->unsignedInteger('pdf_template_id')->nullable();
            $table->foreign('pdf_template_id')->references('id')->on('pdf_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_users', function (Blueprint $table) {
            //
        });
    }
}
