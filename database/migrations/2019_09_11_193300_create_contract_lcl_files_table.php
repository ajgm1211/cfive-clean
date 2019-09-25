<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractLclFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_lcl_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractlcl_id')->unsigned();
            $table->string('namefile');
            $table->foreign('contractlcl_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
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
        Schema::dropIfExists('contract_lcl_files');
    }
}
