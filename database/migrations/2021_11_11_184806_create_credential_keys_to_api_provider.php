<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCredentialKeysToApiProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $credential_keys_1 = json_encode(["behalfOf"]); //CMA RATES
        $credential_keys_2 = json_encode([]); //MAERSK SPOT
        $credential_keys_3 = json_encode([]); //SEALAND SPOT
        $credential_keys_4 = json_encode(["secret", "user_id", "api_key"]); //EVERGREEN SPOT
        $credential_keys_5 = json_encode(["auth_uri", "username", "password"]); //QUICK QUOTES

        DB::table('api_providers') //CMA RATES
            ->where('id', "1")
            ->update(['credential_keys' => $credential_keys_1]);
        DB::table('api_providers') //MAERSK SPOT
            ->where('id', "2")
            ->update(['credential_keys' => $credential_keys_2]);
        DB::table('api_providers') //SEALAND SPOT
            ->where('id', "3")
            ->update(['credential_keys' => $credential_keys_3]);
        DB::table('api_providers') //EVERGREEN SPOT
            ->where('id', "4")
            ->update(['credential_keys' => $credential_keys_4]);
        DB::table('api_providers') //QUICK QUOTES
            ->where('id', "5")
            ->update(['credential_keys' => $credential_keys_5]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('api_providers') //CMA RATES
            ->where('id', "1")
            ->update(['credential_keys' => null]);
        DB::table('api_providers') //MAERSK SPOT
            ->where('id', "2")
            ->update(['credential_keys' => null]);
        DB::table('api_providers') //SEALAND SPOT
            ->where('id', "3")
            ->update(['credential_keys' => null]);
        DB::table('api_providers') //EVERGREEN SPOT
            ->where('id', "4")
            ->update(['credential_keys' => null]);
        DB::table('api_providers') //QUICK QUOTES
            ->where('id', "5")
            ->update(['credential_keys' => null]);
    }
}
