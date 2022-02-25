<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSealandAmericaToApiProviders extends Migration
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
                'name' => 'SEALAND AMERICAS SPOT',
                'code' => 'seau',
                'image' => 'sealand.jpg',
                'description' => '',
                'credential_keys' => json_encode([]),
                'status' => 1,
                'url' => 'https://offers.api.maersk.com/',
                'require_login' => false,
                'credentials' => json_encode([
                    "api_key" => "cargofive-ncfc6t37ZBdkUFqBdmvwBHsKYAkKSWzc"
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
