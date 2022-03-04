<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOneToApiProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('api_providers')->insert(
            array(
                'name' => 'ONE QUOTE',
                'code' => 'one',
                'image' => 'one.png',
                'description' => 'Ocean Network Express',
                'credential_keys' => json_encode(["userId", "currentPassword"]),
                'status' => 1,
                'url' => 'https://cetusapi-prod.kontainers.io/trip-ui/api/v1/customer/',
                'require_login' => false,
                'credentials' => json_encode([
                    "userId" => "ACROSS2018",
                    "currentPassword" => "Hospitalet2021*"
                ])                
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
        Schema::table('api_providers', function (Blueprint $table) {
            //
        });
    }
}
