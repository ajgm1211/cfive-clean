<?php

use Illuminate\Database\Seeder;

class AirlinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('airlines')->delete();
        
        \DB::table('airlines')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'American Airlines',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Copa Airlines',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'United Airlines',
            )
        ));
    }
}