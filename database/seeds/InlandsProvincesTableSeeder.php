<?php

use Illuminate\Database\Seeder;

class InlandsProvincesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inlands_provinces')->delete();
        
        \DB::table('inlands_provinces')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Distrito Capital',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:31:01',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'carabobo',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:32:32',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'zulia',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:32:32',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'cartagena',
                'region' => '',
                'country_id' => 47,
                'created_at' => '2021-06-01 19:33:23',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Alava',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Albacete',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Alicante',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Almeria',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Asturias',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 11,
                'name' => 'Avila',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 12,
                'name' => 'Badajoz',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 13,
                'name' => 'Barcelona',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 14,
                'name' => 'Burgos',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 15,
                'name' => 'Cadiz',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 16,
                'name' => 'Cordoba',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 17,
                'name' => 'Granada',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 18,
                'name' => 'Huelva',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 19,
                'name' => 'Jaen',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 20,
                'name' => 'Malaga',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 21,
                'name' => 'Sevilla',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'Gerona',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'Huesca',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 24,
                'name' => 'Lerida',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 25,
                'name' => 'Tarragona',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 26,
                'name' => 'Teruel',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 27,
                'name' => 'Cantabria',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 28,
                'name' => 'Guipuzcoa',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 29,
                'name' => 'La Rioja',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 30,
                'name' => 'Leon',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 31,
                'name' => 'Navarra',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 32,
                'name' => 'Palencia',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            31 => 
            array (
                'id' => 33,
                'name' => 'Soria',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            32 => 
            array (
                'id' => 34,
                'name' => 'Vizcaya',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            33 => 
            array (
                'id' => 35,
                'name' => 'Zamora',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 36,
                'name' => 'Caceres',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            35 => 
            array (
                'id' => 37,
                'name' => 'Castellon',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            36 => 
            array (
                'id' => 38,
                'name' => 'Ciudad Real',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            37 => 
            array (
                'id' => 39,
                'name' => 'Cuenca',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            38 => 
            array (
                'id' => 40,
                'name' => 'Guadalajara',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            39 => 
            array (
                'id' => 41,
                'name' => 'La Coruña',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            40 => 
            array (
                'id' => 42,
                'name' => 'Lugo',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            41 => 
            array (
                'id' => 43,
                'name' => 'Madrid',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            42 => 
            array (
                'id' => 44,
                'name' => 'Murcia',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            43 => 
            array (
                'id' => 45,
                'name' => 'Orense',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            44 => 
            array (
                'id' => 46,
                'name' => 'Pontevedra',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            45 => 
            array (
                'id' => 47,
                'name' => 'Salamanca',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            46 => 
            array (
                'id' => 48,
                'name' => 'Segovia',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            47 => 
            array (
                'id' => 49,
                'name' => 'Toledo',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            48 => 
            array (
                'id' => 50,
                'name' => 'Valladolid',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            49 => 
            array (
                'id' => 51,
                'name' => 'Zaragoza',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
            50 => 
            array (
                'id' => 52,
                'name' => 'Valencia',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}