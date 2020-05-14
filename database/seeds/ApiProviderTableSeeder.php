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
        
        \DB::table('api_provider')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'CMA CGM',
                'code' => 'CMACGM',
                'created_at' => NULL,
                'updated_at' => NULL,
            ), 
            1 => 
            array (
                'id' => 2,
                'name' => 'MAERSK',
                'code' => 'MAERSK',
                'created_at' => NULL,
                'updated_at' => NULL,
            ), 
            2 => 
            array (
                'id' => 3,
                'name' => 'SAFMARINE',
                'code' => 'SAFMARINE',
                'created_at' => NULL,
                'updated_at' => NULL,
            ), 
        ));
    }
}
