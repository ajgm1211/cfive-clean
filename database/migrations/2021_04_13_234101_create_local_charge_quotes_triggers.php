<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLocalChargeQuotesTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER `after_local_charge_quotes_insert` AFTER INSERT ON `local_charge_quotes`
            FOR EACH ROW BEGIN
                UPDATE integration_quote_statuses set status=0 where quote_id=new.quote_id;
            END
        ');

        DB::unprepared('
        CREATE TRIGGER `after_local_charge_quotes_update` AFTER UPDATE ON `local_charge_quotes`
            FOR EACH ROW BEGIN
                UPDATE integration_quote_statuses set status=0 where quote_id=new.quote_id;
            END
        ');

        DB::unprepared('
        CREATE TRIGGER `after_local_charge_quotes_delete` AFTER DELETE ON `local_charge_quotes`
            FOR EACH ROW BEGIN
                UPDATE integration_quote_statuses set status=0 where quote_id=old.quote_id;
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
        Schema::dropIfExists('local_charge_quotes_triggers');
    }
}
