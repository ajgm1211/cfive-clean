<?php

use Illuminate\Database\Seeder;

class SaleTermTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sale_term_types')->delete();
        
        \DB::table('sale_term_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Origin',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Destination',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}