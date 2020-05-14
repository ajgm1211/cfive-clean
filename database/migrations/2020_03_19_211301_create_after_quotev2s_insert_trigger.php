<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfterQuotev2sInsertTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
                CREATE TRIGGER `after_quotev2s_insert` AFTER INSERT ON `quote_v2s`
                    FOR EACH ROW BEGIN
                        INSERT INTO integration_quote_statuses(quote_id, company_user_id, status)
                        VALUES(new.id,new.company_user_id,0);
                    END
                ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `after_quotev2s_insert`');
    }
}
