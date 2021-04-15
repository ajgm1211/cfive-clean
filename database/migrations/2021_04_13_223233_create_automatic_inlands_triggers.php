<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAutomaticInlandsTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER `after_automatic_inlands_insert` AFTER INSERT ON `automatic_inlands`
            FOR EACH ROW BEGIN
                UPDATE integration_quote_statuses set status=0 where quote_id=new.quote_id;
            END
        ');

        DB::unprepared('
        CREATE TRIGGER `after_automatic_inlands_update` AFTER UPDATE ON `automatic_inlands`
            FOR EACH ROW BEGIN
                UPDATE integration_quote_statuses set status=0 where quote_id=new.quote_id;
            END
        ');

        DB::unprepared('
        CREATE TRIGGER `after_automatic_inlands_delete` AFTER DELETE ON `automatic_inlands`
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
        Schema::dropIfExists('automatic_inlands_triggers');
    }
}
