<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAutomaticRateIdNullableOnAutomaticInlands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE automatic_inlands DROP FOREIGN KEY automatic_inlands_automatic_rate_id_foreign');
        
        \DB::statement('ALTER TABLE automatic_inlands CHANGE automatic_rate_id automatic_rate_id INT(10) UNSIGNED NULL');

        \DB::statement('ALTER TABLE `automatic_inlands` ADD CONSTRAINT `automatic_inlands_automatic_rate_id_foreign` FOREIGN KEY (`automatic_rate_id`) REFERENCES `automatic_rates`(`id`) ON DELETE SET NULL ON UPDATE SET NULL');    }

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
