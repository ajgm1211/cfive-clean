<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateToQuoteV2sView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW        
            `view_quote_v2s` AS (
                SELECT 
                    `quote_v2s`.`id` AS `id`,
                    `quote_v2s`.`quote_id` AS `quote_id`,
                    `quote_v2s`.`custom_quote_id` AS `custom_quote_id`,
                    `quote_v2s`.`status` AS `status`,
                    `companies`.`id` AS `company_id`,
                    `companies`.`business_name` AS `business_name`,
                    JSON_OBJECT('id', `companies`.`id`, 'label', `companies`.`business_name`) AS `company_array`,
                    `quote_v2s`.`type` AS `type`,
                    `quote_v2s`.`created_at` AS `created_at`,
                    
                    (SELECT GROUP_CONCAT(DISTINCT `harbors`.`display_name` SEPARATOR '| ')
                        FROM (`automatic_rates` JOIN `harbors` ON ((`harbors`.`id` = `automatic_rates`.`origin_port_id`)))
                        WHERE ((`quote_v2s`.`id` = `automatic_rates`.`quote_id`)AND ISNULL(`automatic_rates`.`deleted_at`))) AS `origin_port`,
                    
                    (SELECT GROUP_CONCAT(DISTINCT `harbors`.`display_name` SEPARATOR '| ')
                        FROM (`automatic_rates`JOIN `harbors` ON ((`harbors`.`id` = `automatic_rates`.`destination_port_id`)))
                        WHERE ((`quote_v2s`.`id` = `automatic_rates`.`quote_id`) AND ISNULL(`automatic_rates`.`deleted_at`))) AS `destination_port`,
                    
                    (SELECT GROUP_CONCAT(DISTINCT `airports`.`display_name` SEPARATOR '| ')
                        FROM (`automatic_rates`
                        JOIN `airports` ON ((`airports`.`id` = `automatic_rates`.`origin_airport_id`)))
                        WHERE ((`quote_v2s`.`id` = `automatic_rates`.`quote_id`) AND ISNULL(`automatic_rates`.`deleted_at`))) AS `origin_airport`,
                    
                    (SELECT GROUP_CONCAT(DISTINCT `airports`.`display_name` SEPARATOR '| ')
                        FROM (`automatic_rates`
                        JOIN `airports` ON ((`airports`.`id` = `automatic_rates`.`destination_airport_id`)))
                        WHERE ((`quote_v2s`.`id` = `automatic_rates`.`quote_id`) AND ISNULL(`automatic_rates`.`deleted_at`))) AS `destination_airport`,
                    
                    `quote_v2s`.`user_id` AS `user_id`,
                    CONCAT(`users`.`name`, ' ', `users`.`lastname`) AS `owner`,
                    `quote_v2s`.`company_user_id` AS `company_user_id`
                FROM ((`quote_v2s`
                    LEFT JOIN `companies` ON ((`quote_v2s`.`company_id` = `companies`.`id`)))
                    JOIN `users` ON ((`quote_v2s`.`user_id` = `users`.`id`)))
                WHERE
                    ISNULL(`quote_v2s`.`deleted_at`));
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_v2s_view', function (Blueprint $table) {
            //
        });
    }
}
