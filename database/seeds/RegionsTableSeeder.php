<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('regions')->delete();
        
        \DB::table('regions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Europa meridional',
                'created_at' => '2019-04-10 17:06:23',
                'updated_at' => '2019-04-10 17:06:23',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Northern Europe',
                'created_at' => '2019-04-10 17:07:57',
                'updated_at' => '2019-04-10 17:07:57',
            ),
        ));
        
        
    }
}