<?php

use Illuminate\Database\Seeder;

class ContainerCalculationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('container_calculations')->delete();
        
        \DB::table('container_calculations')->insert(array (
            0 => 
            array (
                'id' => 2,
                'container_id' => 1,
                'calculationtype_id' => 2,
            ),
            1 => 
            array (
                'id' => 3,
                'container_id' => 1,
                'calculationtype_id' => 4,
            ),
            2 => 
            array (
                'id' => 4,
                'container_id' => 1,
                'calculationtype_id' => 5,
            ),
            3 => 
            array (
                'id' => 5,
                'container_id' => 1,
                'calculationtype_id' => 6,
            ),
            4 => 
            array (
                'id' => 6,
                'container_id' => 1,
                'calculationtype_id' => 9,
            ),
            5 => 
            array (
                'id' => 7,
                'container_id' => 1,
                'calculationtype_id' => 10,
            ),
            6 => 
            array (
                'id' => 8,
                'container_id' => 1,
                'calculationtype_id' => 11,
            ),
            7 => 
            array (
                'id' => 9,
                'container_id' => 2,
                'calculationtype_id' => 1,
            ),
            8 => 
            array (
                'id' => 10,
                'container_id' => 2,
                'calculationtype_id' => 4,
            ),
            9 => 
            array (
                'id' => 11,
                'container_id' => 2,
                'calculationtype_id' => 5,
            ),
            10 => 
            array (
                'id' => 12,
                'container_id' => 2,
                'calculationtype_id' => 6,
            ),
            11 => 
            array (
                'id' => 13,
                'container_id' => 2,
                'calculationtype_id' => 9,
            ),
            12 => 
            array (
                'id' => 14,
                'container_id' => 2,
                'calculationtype_id' => 10,
            ),
            13 => 
            array (
                'id' => 15,
                'container_id' => 2,
                'calculationtype_id' => 11,
            ),
            14 => 
            array (
                'id' => 16,
                'container_id' => 3,
                'calculationtype_id' => 3,
            ),
            15 => 
            array (
                'id' => 17,
                'container_id' => 3,
                'calculationtype_id' => 4,
            ),
            16 => 
            array (
                'id' => 18,
                'container_id' => 3,
                'calculationtype_id' => 5,
            ),
            17 => 
            array (
                'id' => 19,
                'container_id' => 3,
                'calculationtype_id' => 6,
            ),
            18 => 
            array (
                'id' => 20,
                'container_id' => 3,
                'calculationtype_id' => 9,
            ),
            19 => 
            array (
                'id' => 21,
                'container_id' => 3,
                'calculationtype_id' => 10,
            ),
            20 => 
            array (
                'id' => 22,
                'container_id' => 3,
                'calculationtype_id' => 11,
            ),
            21 => 
            array (
                'id' => 23,
                'container_id' => 4,
                'calculationtype_id' => 8,
            ),
            22 => 
            array (
                'id' => 24,
                'container_id' => 4,
                'calculationtype_id' => 4,
            ),
            23 => 
            array (
                'id' => 25,
                'container_id' => 4,
                'calculationtype_id' => 5,
            ),
            24 => 
            array (
                'id' => 26,
                'container_id' => 4,
                'calculationtype_id' => 6,
            ),
            25 => 
            array (
                'id' => 27,
                'container_id' => 4,
                'calculationtype_id' => 9,
            ),
            26 => 
            array (
                'id' => 28,
                'container_id' => 4,
                'calculationtype_id' => 10,
            ),
            27 => 
            array (
                'id' => 29,
                'container_id' => 4,
                'calculationtype_id' => 11,
            ),
            28 => 
            array (
                'id' => 30,
                'container_id' => 5,
                'calculationtype_id' => 7,
            ),
            29 => 
            array (
                'id' => 31,
                'container_id' => 5,
                'calculationtype_id' => 4,
            ),
            30 => 
            array (
                'id' => 32,
                'container_id' => 5,
                'calculationtype_id' => 5,
            ),
            31 => 
            array (
                'id' => 33,
                'container_id' => 5,
                'calculationtype_id' => 6,
            ),
            32 => 
            array (
                'id' => 34,
                'container_id' => 5,
                'calculationtype_id' => 9,
            ),
            33 => 
            array (
                'id' => 35,
                'container_id' => 5,
                'calculationtype_id' => 10,
            ),
            34 => 
            array (
                'id' => 36,
                'container_id' => 5,
                'calculationtype_id' => 11,
            ),
            35 => 
            array (
                'id' => 37,
                'container_id' => 6,
                'calculationtype_id' => 21,
            ),
            36 => 
            array (
                'id' => 38,
                'container_id' => 6,
                'calculationtype_id' => 12,
            ),
            37 => 
            array (
                'id' => 39,
                'container_id' => 7,
                'calculationtype_id' => 13,
            ),
            38 => 
            array (
                'id' => 40,
                'container_id' => 8,
                'calculationtype_id' => 14,
            ),
            39 => 
            array (
                'id' => 41,
                'container_id' => 11,
                'calculationtype_id' => 25,
            ),
            40 => 
            array (
                'id' => 42,
                'container_id' => 12,
                'calculationtype_id' => 26,
            ),
            41 => 
            array (
                'id' => 44,
                'container_id' => 10,
                'calculationtype_id' => 18,
            ),
            42 => 
            array (
                'id' => 45,
                'container_id' => 9,
                'calculationtype_id' => 16,
            ),
        ));
        
        
    }
}