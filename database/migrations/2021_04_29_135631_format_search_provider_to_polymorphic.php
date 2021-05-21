<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormatSearchProviderToPolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_carriers', function (Blueprint $table) {
            $table->bigInteger('provider_id')->unsigned()->change();
            $table->string('provider_type')->after('provider_id');
            $table->index(['provider_id', 'provider_type']);
        });

        DB::table('search_carriers')->update(
            array(
                'provider_type' => 'App\Carrier',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_carriers', function (Blueprint $table) {
            //
        });
    }
}
