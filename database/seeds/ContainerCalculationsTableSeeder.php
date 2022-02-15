<?php

use Illuminate\Database\Seeder;

class ContainerCalculationsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('container_calculations')->delete();

        \DB::table('container_calculations')->insert([
            0 => [
                'id' => 2,
                'container_id' => 1,
                'calculationtype_id' => 2,
            ],
            1 => [
                'id' => 3,
                'container_id' => 1,
                'calculationtype_id' => 4,
            ],
            2 => [
                'id' => 4,
                'container_id' => 1,
                'calculationtype_id' => 5,
            ],
            3 => [
                'id' => 5,
                'container_id' => 1,
                'calculationtype_id' => 6,
            ],
            4 => [
                'id' => 6,
                'container_id' => 1,
                'calculationtype_id' => 9,
            ],
            5 => [
                'id' => 7,
                'container_id' => 1,
                'calculationtype_id' => 10,
            ],
            6 => [
                'id' => 8,
                'container_id' => 1,
                'calculationtype_id' => 11,
            ],
            7 => [
                'id' => 9,
                'container_id' => 2,
                'calculationtype_id' => 1,
            ],
            8 => [
                'id' => 10,
                'container_id' => 2,
                'calculationtype_id' => 4,
            ],
            9 => [
                'id' => 11,
                'container_id' => 2,
                'calculationtype_id' => 5,
            ],
            10 => [
                'id' => 12,
                'container_id' => 2,
                'calculationtype_id' => 6,
            ],
            11 => [
                'id' => 13,
                'container_id' => 2,
                'calculationtype_id' => 9,
            ],
            12 => [
                'id' => 14,
                'container_id' => 2,
                'calculationtype_id' => 10,
            ],
            13 => [
                'id' => 15,
                'container_id' => 2,
                'calculationtype_id' => 11,
            ],
            14 => [
                'id' => 16,
                'container_id' => 3,
                'calculationtype_id' => 3,
            ],
            15 => [
                'id' => 17,
                'container_id' => 3,
                'calculationtype_id' => 4,
            ],
            16 => [
                'id' => 18,
                'container_id' => 3,
                'calculationtype_id' => 5,
            ],
            17 => [
                'id' => 19,
                'container_id' => 3,
                'calculationtype_id' => 6,
            ],
            18 => [
                'id' => 20,
                'container_id' => 3,
                'calculationtype_id' => 9,
            ],
            19 => [
                'id' => 21,
                'container_id' => 3,
                'calculationtype_id' => 10,
            ],
            20 => [
                'id' => 22,
                'container_id' => 3,
                'calculationtype_id' => 11,
            ],
            21 => [
                'id' => 23,
                'container_id' => 4,
                'calculationtype_id' => 8,
            ],
            22 => [
                'id' => 24,
                'container_id' => 4,
                'calculationtype_id' => 4,
            ],
            23 => [
                'id' => 25,
                'container_id' => 4,
                'calculationtype_id' => 5,
            ],
            24 => [
                'id' => 26,
                'container_id' => 4,
                'calculationtype_id' => 6,
            ],
            25 => [
                'id' => 27,
                'container_id' => 4,
                'calculationtype_id' => 9,
            ],
            26 => [
                'id' => 28,
                'container_id' => 4,
                'calculationtype_id' => 10,
            ],
            27 => [
                'id' => 29,
                'container_id' => 4,
                'calculationtype_id' => 11,
            ],
            28 => [
                'id' => 30,
                'container_id' => 5,
                'calculationtype_id' => 7,
            ],
            29 => [
                'id' => 31,
                'container_id' => 5,
                'calculationtype_id' => 4,
            ],
            30 => [
                'id' => 32,
                'container_id' => 5,
                'calculationtype_id' => 5,
            ],
            31 => [
                'id' => 33,
                'container_id' => 5,
                'calculationtype_id' => 6,
            ],
            32 => [
                'id' => 34,
                'container_id' => 5,
                'calculationtype_id' => 9,
            ],
            33 => [
                'id' => 35,
                'container_id' => 5,
                'calculationtype_id' => 10,
            ],
            34 => [
                'id' => 36,
                'container_id' => 5,
                'calculationtype_id' => 11,
            ],
            35 => [
                'id' => 38,
                'container_id' => 6,
                'calculationtype_id' => 12,
            ],
            36 => [
                'id' => 39,
                'container_id' => 7,
                'calculationtype_id' => 13,
            ],
            37 => [
                'id' => 40,
                'container_id' => 8,
                'calculationtype_id' => 14,
            ],
            38 => [
                'id' => 41,
                'container_id' => 11,
                'calculationtype_id' => 25,
            ],
            39 => [
                'id' => 42,
                'container_id' => 12,
                'calculationtype_id' => 26,
            ],
            40 => [
                'id' => 44,
                'container_id' => 10,
                'calculationtype_id' => 18,
            ],
            41 => [
                'id' => 45,
                'container_id' => 9,
                'calculationtype_id' => 16,
            ],
            42 => [
                'id' => 46,
                'container_id' => 6,
                'calculationtype_id' => 19,
            ],
        ]);
    }
}
