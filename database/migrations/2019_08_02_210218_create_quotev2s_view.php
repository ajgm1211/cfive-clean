<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotev2sView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW view_quote_v2s AS (select quote_v2s.id, quote_v2s.quote_id, quote_v2s.custom_quote_id, companies.business_name, quote_v2s.created_at, CONCAT(users.name," ", users.lastname) as owner, (SELECT GROUP_CONCAT(DISTINCT(harbors.display_name) SEPARATOR '| ') FROM automatic_rates INNER JOIN harbors ON harbors.id = automatic_rates.origin_port_id WHERE quote_v2s.id=automatic_rates.quote_id) as origin_port, (SELECT GROUP_CONCAT(DISTINCT(harbors.display_name) SEPARATOR '| ') FROM automatic_rates INNER JOIN harbors ON harbors.id = automatic_rates.destination_port_id WHERE quote_v2s.id=automatic_rates.quote_id) as destination_port, quote_v2s.type FROM quote_v2s LEFT JOIN companies ON quote_v2s.company_id=companies.id INNER JOIN users ON quote_v2s.user_id = users.id)");
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
