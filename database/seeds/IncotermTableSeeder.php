<?php

use Illuminate\Database\Seeder;

class IncotermTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('incoterms')->delete();
        
        \DB::table('incoterms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'FOB',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'EWX',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'FCA',
            ),
        ));
        
        
    }
}