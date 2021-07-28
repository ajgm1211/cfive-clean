<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAutomaticRateIdNullableOnAutomaticInlandsLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE automatic_inland_lcl_airs DROP FOREIGN KEY automatic_inland_lcl_airs_automatic_rate_id_foreign');
        
        \DB::statement('ALTER TABLE automatic_inland_lcl_airs CHANGE automatic_rate_id automatic_rate_id INT(10) UNSIGNED NULL');
        
        \DB::statement('ALTER TABLE `automatic_inland_lcl_airs` ADD CONSTRAINT `automatic_inland_lcl_airs_automatic_rate_id_foreign` FOREIGN KEY (`automatic_rate_id`) REFERENCES `automatic_rates`(`id`) ON DELETE SET NULL ON UPDATE SET NULL');
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
