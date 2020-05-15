<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableNewRequest extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		DB::unprepared('ALTER TABLE `newcontractrequests` CHANGE `namefile` `namefile` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
		ALTER TABLE newcontractrequests DROP COLUMN type;');
	}

	/**
     * Reverse the migrations.
     *
     * @return void
     */
	public function down()
	{
		//
	}
}
