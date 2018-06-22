<?php

use Illuminate\Database\Seeder;

class TypedestinyTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('typedestiny')->delete();
        
        \DB::table('typedestiny')->insert(array (
            0 => 
            array (
                'id' => 1,
                'description' => 'origin',
            ),
            1 => 
            array (
                'id' => 2,
                'description' => 'destiny',
            ),
            2 => 
            array (
                'id' => 3,
                'description' => 'freight',
            ),
        ));
        
        
    }
}