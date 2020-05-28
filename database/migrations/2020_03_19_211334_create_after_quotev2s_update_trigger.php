<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfterQuotev2sUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER `after_quotev2s_update` AFTER UPDATE ON `quote_v2s`
                FOR EACH ROW BEGIN
                    UPDATE integration_quote_statuses set status=0 where quote_id=new.id;
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
        //
    }
}
