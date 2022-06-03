<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuoteV2sView extends Migration
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
                select 
                    `quote_v2s`.`id` AS `id`,
                    `quote_v2s`.`quote_id` AS `quote_id`,
                    `quote_v2s`.`custom_quote_id` AS `custom_quote_id`,
                    `quote_v2s`.`status` AS `status`,
                    `companies`.`id` AS `company_id`,
                    `companies`.`business_name` AS `business_name`,
                    `quote_v2s`.`type` AS `type`,
                    `quote_v2s`.`created_at` AS `created_at`,
                    (select group_concat(distinct `harbors`.`display_name` separator '| ') 
                    from (`automatic_rates` join `harbors` on((`harbors`.`id` = `automatic_rates`.`origin_port_id`))) 
                    where (`quote_v2s`.`id` = `automatic_rates`.`quote_id` and ISNULL(`automatic_rates`.`deleted_at`))) AS `origin_port`,
                    (select group_concat(distinct `harbors`.`display_name` separator '| ') 
                    from (`automatic_rates` join `harbors` on((`harbors`.`id` = `automatic_rates`.`destination_port_id`))) 
                    where (`quote_v2s`.`id` = `automatic_rates`.`quote_id` and ISNULL(`automatic_rates`.`deleted_at`))) AS `destination_port`,
                    (select group_concat(distinct `airports`.`display_name` separator '| ') 
                    from (`automatic_rates` join `airports` on((`airports`.`id` = `automatic_rates`.`origin_airport_id`))) 
                    where (`quote_v2s`.`id` = `automatic_rates`.`quote_id` and ISNULL(`automatic_rates`.`deleted_at`))) AS `origin_airport`,
                    (select group_concat(distinct `airports`.`display_name` separator '| ') 
                    from (`automatic_rates` join `airports` on((`airports`.`id` = `automatic_rates`.`destination_airport_id`))) 
                    where (`quote_v2s`.`id` = `automatic_rates`.`quote_id` and ISNULL(`automatic_rates`.`deleted_at`))) AS `destination_airport`,
                    `quote_v2s`.`user_id` AS `user_id`,
                    concat(`users`.`name`,' ',`users`.`lastname`) AS `owner`,
                    `quote_v2s`.`company_user_id` AS `company_user_id`
                from ((`quote_v2s` 
                left join `companies` on((`quote_v2s`.`company_id` = `companies`.`id`))) 
                join `users` on((`quote_v2s`.`user_id` = `users`.`id`))) 
                WHERE ISNULL(`quote_v2s`.`deleted_at`)
            );
        ");
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
