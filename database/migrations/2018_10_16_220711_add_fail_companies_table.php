<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFailCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fail_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('business_name');
            $table->string('phone');
            $table->string('address',200)->nullable();
            $table->string('email');
            $table->string('tax_number');
            $table->integer('associated_quotes')->nullable();
            $table->integer('company_user_id')->unsigned();
            $table->integer('owner')->unsigned();
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
        Schema::dropIfExists('fail_companies');
    }
}
