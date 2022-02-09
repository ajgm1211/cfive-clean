<?php

use Illuminate\Database\Seeder;

class InlandProvincesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inland_provinces')->delete();
        
        \DB::table('inland_provinces')->insert(array (
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
                'name' => 'CARTAGENA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-06-01 19:33:23',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'ALAVA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'ALBACETE',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'ALICANTE',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'ALMERIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'ASTURIAS',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            9 => 
            array (
                'id' => 11,
                'name' => 'AVILA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            10 => 
            array (
                'id' => 12,
                'name' => 'BADAJOZ',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            11 => 
            array (
                'id' => 13,
                'name' => 'BARCELONA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            12 => 
            array (
                'id' => 14,
                'name' => 'BURGOS',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            13 => 
            array (
                'id' => 15,
                'name' => 'CADIZ',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            14 => 
            array (
                'id' => 16,
                'name' => 'CORDOBA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            15 => 
            array (
                'id' => 17,
                'name' => 'GRANADA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            16 => 
            array (
                'id' => 18,
                'name' => 'HUELVA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            17 => 
            array (
                'id' => 19,
                'name' => 'JAEN',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            18 => 
            array (
                'id' => 20,
                'name' => 'MALAGA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            19 => 
            array (
                'id' => 21,
                'name' => 'SEVILLA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'GERONA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'HUESCA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            22 => 
            array (
                'id' => 24,
                'name' => 'LERIDA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            23 => 
            array (
                'id' => 25,
                'name' => 'TARRAGONA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            24 => 
            array (
                'id' => 26,
                'name' => 'TERUEL',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            25 => 
            array (
                'id' => 27,
                'name' => 'CANTABRIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            26 => 
            array (
                'id' => 28,
                'name' => 'GUIPUZCOA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            27 => 
            array (
                'id' => 29,
                'name' => 'LA RIOJA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            28 => 
            array (
                'id' => 30,
                'name' => 'LEON',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            29 => 
            array (
                'id' => 31,
                'name' => 'NAVARRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            30 => 
            array (
                'id' => 32,
                'name' => 'PALENCIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            31 => 
            array (
                'id' => 33,
                'name' => 'SORIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            32 => 
            array (
                'id' => 34,
                'name' => 'VIZCAYA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            33 => 
            array (
                'id' => 35,
                'name' => 'ZAMORA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            34 => 
            array (
                'id' => 36,
                'name' => 'CACERES',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            35 => 
            array (
                'id' => 37,
                'name' => 'CASTELLON',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            36 => 
            array (
                'id' => 38,
                'name' => 'CIUDAD REAL',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            37 => 
            array (
                'id' => 39,
                'name' => 'CUENCA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            38 => 
            array (
                'id' => 40,
                'name' => 'GUADALAJARA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
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
                'name' => 'LUGO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            41 => 
            array (
                'id' => 43,
                'name' => 'MADRID',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            42 => 
            array (
                'id' => 44,
                'name' => 'MURCIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            43 => 
            array (
                'id' => 45,
                'name' => 'ORENSE',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            44 => 
            array (
                'id' => 46,
                'name' => 'PONTEVEDRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            45 => 
            array (
                'id' => 47,
                'name' => 'SALAMANCA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            46 => 
            array (
                'id' => 48,
                'name' => 'SEGOVIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            47 => 
            array (
                'id' => 49,
                'name' => 'TOLEDO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            48 => 
            array (
                'id' => 50,
                'name' => 'VALLADOLID',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            49 => 
            array (
                'id' => 51,
                'name' => 'ZARAGOZA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            50 => 
            array (
                'id' => 52,
                'name' => 'VALENCIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2021-08-03 00:00:00',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            51 => 
            array (
                'id' => 53,
                'name' => 'ALEMANIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            52 => 
            array (
                'id' => 54,
                'name' => 'ANDORRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            53 => 
            array (
                'id' => 55,
                'name' => 'BADALONA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            54 => 
            array (
                'id' => 56,
                'name' => 'BALEARES',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            55 => 
            array (
                'id' => 57,
                'name' => 'BELGICA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            56 => 
            array (
                'id' => 58,
                'name' => 'BILBAO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:41:15',
                'updated_at' => '2022-01-23 15:54:45',
            ),
            57 => 
            array (
                'id' => 59,
                'name' => 'CORUÑA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            58 => 
            array (
                'id' => 60,
                'name' => 'ELCHE',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            59 => 
            array (
                'id' => 61,
                'name' => 'FRANCIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            60 => 
            array (
                'id' => 62,
                'name' => 'INGLATERRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            61 => 
            array (
                'id' => 63,
                'name' => 'ITALIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            62 => 
            array (
                'id' => 64,
                'name' => 'LOGROÑO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            63 => 
            array (
                'id' => 65,
                'name' => 'MALLORCA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            64 => 
            array (
                'id' => 66,
                'name' => 'OVIEDO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            65 => 
            array (
                'id' => 67,
                'name' => 'PAMPLONA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            66 => 
            array (
                'id' => 68,
                'name' => 'PORTUGAL',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            67 => 
            array (
                'id' => 69,
                'name' => 'RIOJA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            68 => 
            array (
                'id' => 70,
                'name' => 'SAN SEBASTIAN',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            69 => 
            array (
                'id' => 71,
                'name' => 'SUIZA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            70 => 
            array (
                'id' => 72,
                'name' => 'VIGO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            71 => 
            array (
                'id' => 73,
                'name' => 'VITORIA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            72 => 
            array (
                'id' => 74,
                'name' => 'SANTANDER',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            73 => 
            array (
                'id' => 75,
                'name' => 'SAGRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            74 => 
            array (
                'id' => 76,
                'name' => 'SAGUNTO',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            75 => 
            array (
                'id' => 77,
                'name' => 'PARRA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            76 => 
            array (
                'id' => 78,
                'name' => 'PERPIGNAN',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            77 => 
            array (
                'id' => 79,
                'name' => 'HOLANDA',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            78 => 
            array (
                'id' => 80,
                'name' => 'DONOSTI',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
            79 => 
            array (
                'id' => 81,
                'name' => 'GIJON',
                'region' => '',
                'country_id' => 66,
                'created_at' => '2022-01-23 15:46:28',
                'updated_at' => '2022-01-23 15:46:28',
            ),
        ));
        
        
    }
}