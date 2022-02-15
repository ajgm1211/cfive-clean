<?php

use Illuminate\Database\Seeder;

class ApiProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('api_provider')->delete();

        \DB::table('api_provider')->insert([
            0 => [
                'id' => 1,
                'name' => 'CMA CGM',
                'code' => 'CMACGM',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'MAERSK',
                'code' => 'MAERSK',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'SAFMARINE',
                'code' => 'SAFMARINE',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
