<?php

use Illuminate\Database\Seeder;

class ProvincesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('provinces')->delete();
        
        \DB::table('provinces')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'ALAVA',
                'region' => NULL,
                'country_id' => 66,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ALBACETE',
                'region' => NULL,
                'country_id' => 66,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'ALEMANIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'ALICANTE',
                'region' => NULL,
                'country_id' => 66,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'ALMERIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'ANDORRA',
                'region' => NULL,
                'country_id' => 66,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'ASTURIAS',
                'region' => NULL,
                'country_id' => 66,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'AVILA',
                'region' => NULL,
                'country_id' => 66,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'BADAJOZ',
                'region' => NULL,
                'country_id' => 66,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'BADALONA',
                'region' => NULL,
                'country_id' => 66,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'BALEARES',
                'region' => NULL,
                'country_id' => 66,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'BARCELONA',
                'region' => NULL,
                'country_id' => 66,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'BELGICA',
                'region' => NULL,
                'country_id' => 66,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'BILBAO',
                'region' => NULL,
                'country_id' => 66,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'BURGOS',
                'region' => NULL,
                'country_id' => 66,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'CACERES',
                'region' => NULL,
                'country_id' => 66,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'CADIZ',
                'region' => NULL,
                'country_id' => 66,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'CANTABRIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'CASTELLON',
                'region' => NULL,
                'country_id' => 66,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'CIUDAD REAL',
                'region' => NULL,
                'country_id' => 66,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'CORDOBA',
                'region' => NULL,
                'country_id' => 66,
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'CORUÑA',
                'region' => NULL,
                'country_id' => 66,
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'CUENCA',
                'region' => NULL,
                'country_id' => 66,
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'ELCHE',
                'region' => NULL,
                'country_id' => 66,
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'FRANCIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'GERONA',
                'region' => NULL,
                'country_id' => 66,
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'GRANADA',
                'region' => NULL,
                'country_id' => 66,
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'GUADALAJARA',
                'region' => NULL,
                'country_id' => 66,
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'GUIPUZCOA',
                'region' => NULL,
                'country_id' => 66,
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'HUELVA',
                'region' => NULL,
                'country_id' => 66,
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'HUESCA',
                'region' => NULL,
                'country_id' => 66,
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'INGLATERRA',
                'region' => NULL,
                'country_id' => 66,
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'ITALIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'JAEN',
                'region' => NULL,
                'country_id' => 66,
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'LA RIOJA',
                'region' => NULL,
                'country_id' => 66,
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'LEON',
                'region' => NULL,
                'country_id' => 66,
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'LERIDA',
                'region' => NULL,
                'country_id' => 66,
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'LOGROÑO',
                'region' => NULL,
                'country_id' => 66,
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'LUGO',
                'region' => NULL,
                'country_id' => 66,
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'MADRID',
                'region' => NULL,
                'country_id' => 66,
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'MALAGA',
                'region' => NULL,
                'country_id' => 66,
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'MALLORCA',
                'region' => NULL,
                'country_id' => 66,
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'MURCIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'NAVARRA',
                'region' => NULL,
                'country_id' => 66,
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'ORENSE',
                'region' => NULL,
                'country_id' => 66,
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'OVIEDO',
                'region' => NULL,
                'country_id' => 66,
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'PALENCIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'PAMPLONA',
                'region' => NULL,
                'country_id' => 66,
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'PONTEVEDRA',
                'region' => NULL,
                'country_id' => 66,
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'PORTUGAL',
                'region' => NULL,
                'country_id' => 66,
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'RIOJA',
                'region' => NULL,
                'country_id' => 66,
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'SALAMANCA',
                'region' => NULL,
                'country_id' => 66,
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'SAN SEBASTIAN',
                'region' => NULL,
                'country_id' => 66,
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'SEGOVIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'SEVILLA',
                'region' => NULL,
                'country_id' => 66,
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'SORIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'SUIZA',
                'region' => NULL,
                'country_id' => 66,
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'TARRAGONA',
                'region' => NULL,
                'country_id' => 66,
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'TERUEL',
                'region' => NULL,
                'country_id' => 66,
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'TOLEDO',
                'region' => NULL,
                'country_id' => 66,
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'VALENCIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'VALLADOLID',
                'region' => NULL,
                'country_id' => 66,
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'VIGO',
                'region' => NULL,
                'country_id' => 66,
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'VITORIA',
                'region' => NULL,
                'country_id' => 66,
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'VIZCAYA',
                'region' => NULL,
                'country_id' => 66,
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'ZAMORA',
                'region' => NULL,
                'country_id' => 66,
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'ZARAGOZA',
                'region' => NULL,
                'country_id' => 66,
            ),
        ));
        
        
    }
}