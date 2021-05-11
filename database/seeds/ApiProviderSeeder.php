<?php

use Illuminate\Database\Seeder;

class ApiProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('api_providers')->insert([
            'name' => 'SEALAND SPOT',
            'status' => 1,
            'image' => 'sealand.png',
            'code' => 'sealand',
            'require_login' => 0
        ]);
    }
}
