<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoscoToApiProviders extends Migration
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
                'name' => 'SYNCONHUB',
                'code' => 'cosco',
                'image' => 'cosco.png',
                'description' => '',
                'credential_keys' => json_encode(["auth_uri","username", "password"]),
                'status' => 1,
                'url' => 'https://synconhub.coscoshipping.com/',
                'require_login' => false,
                'credentials' => json_encode([
                    "auth_uri" => "https://synconhub.coscoshipping.com/api/admin/user/login",
                    "username" => "ACROSSLOGISTICS",
                    "password" => "Hospitalet2021*"
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
