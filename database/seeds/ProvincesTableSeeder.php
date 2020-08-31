<?php

use Illuminate\Database\Seeder;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('provinces')->delete();

        \DB::table('provinces')->insert([
            0 => [
                'id' => 1,
                'name' => 'ALAVA',
                'region' => null,
                'country_id' => 66,
            ],
            1 => [
                'id' => 2,
                'name' => 'ALBACETE',
                'region' => null,
                'country_id' => 66,
            ],
            2 => [
                'id' => 3,
                'name' => 'ALEMANIA',
                'region' => null,
                'country_id' => 66,
            ],
            3 => [
                'id' => 4,
                'name' => 'ALICANTE',
                'region' => null,
                'country_id' => 66,
            ],
            4 => [
                'id' => 5,
                'name' => 'ALMERIA',
                'region' => null,
                'country_id' => 66,
            ],
            5 => [
                'id' => 6,
                'name' => 'ANDORRA',
                'region' => null,
                'country_id' => 66,
            ],
            6 => [
                'id' => 7,
                'name' => 'ASTURIAS',
                'region' => null,
                'country_id' => 66,
            ],
            7 => [
                'id' => 8,
                'name' => 'AVILA',
                'region' => null,
                'country_id' => 66,
            ],
            8 => [
                'id' => 9,
                'name' => 'BADAJOZ',
                'region' => null,
                'country_id' => 66,
            ],
            9 => [
                'id' => 10,
                'name' => 'BADALONA',
                'region' => null,
                'country_id' => 66,
            ],
            10 => [
                'id' => 11,
                'name' => 'BALEARES',
                'region' => null,
                'country_id' => 66,
            ],
            11 => [
                'id' => 12,
                'name' => 'BARCELONA',
                'region' => null,
                'country_id' => 66,
            ],
            12 => [
                'id' => 13,
                'name' => 'BELGICA',
                'region' => null,
                'country_id' => 66,
            ],
            13 => [
                'id' => 14,
                'name' => 'BILBAO',
                'region' => null,
                'country_id' => 66,
            ],
            14 => [
                'id' => 15,
                'name' => 'BURGOS',
                'region' => null,
                'country_id' => 66,
            ],
            15 => [
                'id' => 16,
                'name' => 'CACERES',
                'region' => null,
                'country_id' => 66,
            ],
            16 => [
                'id' => 17,
                'name' => 'CADIZ',
                'region' => null,
                'country_id' => 66,
            ],
            17 => [
                'id' => 18,
                'name' => 'CANTABRIA',
                'region' => null,
                'country_id' => 66,
            ],
            18 => [
                'id' => 19,
                'name' => 'CASTELLON',
                'region' => null,
                'country_id' => 66,
            ],
            19 => [
                'id' => 20,
                'name' => 'CIUDAD REAL',
                'region' => null,
                'country_id' => 66,
            ],
            20 => [
                'id' => 21,
                'name' => 'CORDOBA',
                'region' => null,
                'country_id' => 66,
            ],
            21 => [
                'id' => 22,
                'name' => 'CORUÑA',
                'region' => null,
                'country_id' => 66,
            ],
            22 => [
                'id' => 23,
                'name' => 'CUENCA',
                'region' => null,
                'country_id' => 66,
            ],
            23 => [
                'id' => 24,
                'name' => 'ELCHE',
                'region' => null,
                'country_id' => 66,
            ],
            24 => [
                'id' => 25,
                'name' => 'FRANCIA',
                'region' => null,
                'country_id' => 66,
            ],
            25 => [
                'id' => 26,
                'name' => 'GERONA',
                'region' => null,
                'country_id' => 66,
            ],
            26 => [
                'id' => 27,
                'name' => 'GRANADA',
                'region' => null,
                'country_id' => 66,
            ],
            27 => [
                'id' => 28,
                'name' => 'GUADALAJARA',
                'region' => null,
                'country_id' => 66,
            ],
            28 => [
                'id' => 29,
                'name' => 'GUIPUZCOA',
                'region' => null,
                'country_id' => 66,
            ],
            29 => [
                'id' => 30,
                'name' => 'HUELVA',
                'region' => null,
                'country_id' => 66,
            ],
            30 => [
                'id' => 31,
                'name' => 'HUESCA',
                'region' => null,
                'country_id' => 66,
            ],
            31 => [
                'id' => 32,
                'name' => 'INGLATERRA',
                'region' => null,
                'country_id' => 66,
            ],
            32 => [
                'id' => 33,
                'name' => 'ITALIA',
                'region' => null,
                'country_id' => 66,
            ],
            33 => [
                'id' => 34,
                'name' => 'JAEN',
                'region' => null,
                'country_id' => 66,
            ],
            34 => [
                'id' => 35,
                'name' => 'LA RIOJA',
                'region' => null,
                'country_id' => 66,
            ],
            35 => [
                'id' => 36,
                'name' => 'LEON',
                'region' => null,
                'country_id' => 66,
            ],
            36 => [
                'id' => 37,
                'name' => 'LERIDA',
                'region' => null,
                'country_id' => 66,
            ],
            37 => [
                'id' => 38,
                'name' => 'LOGROÑO',
                'region' => null,
                'country_id' => 66,
            ],
            38 => [
                'id' => 39,
                'name' => 'LUGO',
                'region' => null,
                'country_id' => 66,
            ],
            39 => [
                'id' => 40,
                'name' => 'MADRID',
                'region' => null,
                'country_id' => 66,
            ],
            40 => [
                'id' => 41,
                'name' => 'MALAGA',
                'region' => null,
                'country_id' => 66,
            ],
            41 => [
                'id' => 42,
                'name' => 'MALLORCA',
                'region' => null,
                'country_id' => 66,
            ],
            42 => [
                'id' => 43,
                'name' => 'MURCIA',
                'region' => null,
                'country_id' => 66,
            ],
            43 => [
                'id' => 44,
                'name' => 'NAVARRA',
                'region' => null,
                'country_id' => 66,
            ],
            44 => [
                'id' => 45,
                'name' => 'ORENSE',
                'region' => null,
                'country_id' => 66,
            ],
            45 => [
                'id' => 46,
                'name' => 'OVIEDO',
                'region' => null,
                'country_id' => 66,
            ],
            46 => [
                'id' => 47,
                'name' => 'PALENCIA',
                'region' => null,
                'country_id' => 66,
            ],
            47 => [
                'id' => 48,
                'name' => 'PAMPLONA',
                'region' => null,
                'country_id' => 66,
            ],
            48 => [
                'id' => 49,
                'name' => 'PONTEVEDRA',
                'region' => null,
                'country_id' => 66,
            ],
            49 => [
                'id' => 50,
                'name' => 'PORTUGAL',
                'region' => null,
                'country_id' => 66,
            ],
            50 => [
                'id' => 51,
                'name' => 'RIOJA',
                'region' => null,
                'country_id' => 66,
            ],
            51 => [
                'id' => 52,
                'name' => 'SALAMANCA',
                'region' => null,
                'country_id' => 66,
            ],
            52 => [
                'id' => 53,
                'name' => 'SAN SEBASTIAN',
                'region' => null,
                'country_id' => 66,
            ],
            53 => [
                'id' => 54,
                'name' => 'SEGOVIA',
                'region' => null,
                'country_id' => 66,
            ],
            54 => [
                'id' => 55,
                'name' => 'SEVILLA',
                'region' => null,
                'country_id' => 66,
            ],
            55 => [
                'id' => 56,
                'name' => 'SORIA',
                'region' => null,
                'country_id' => 66,
            ],
            56 => [
                'id' => 57,
                'name' => 'SUIZA',
                'region' => null,
                'country_id' => 66,
            ],
            57 => [
                'id' => 58,
                'name' => 'TARRAGONA',
                'region' => null,
                'country_id' => 66,
            ],
            58 => [
                'id' => 59,
                'name' => 'TERUEL',
                'region' => null,
                'country_id' => 66,
            ],
            59 => [
                'id' => 60,
                'name' => 'TOLEDO',
                'region' => null,
                'country_id' => 66,
            ],
            60 => [
                'id' => 61,
                'name' => 'VALENCIA',
                'region' => null,
                'country_id' => 66,
            ],
            61 => [
                'id' => 62,
                'name' => 'VALLADOLID',
                'region' => null,
                'country_id' => 66,
            ],
            62 => [
                'id' => 63,
                'name' => 'VIGO',
                'region' => null,
                'country_id' => 66,
            ],
            63 => [
                'id' => 64,
                'name' => 'VITORIA',
                'region' => null,
                'country_id' => 66,
            ],
            64 => [
                'id' => 65,
                'name' => 'VIZCAYA',
                'region' => null,
                'country_id' => 66,
            ],
            65 => [
                'id' => 66,
                'name' => 'ZAMORA',
                'region' => null,
                'country_id' => 66,
            ],
            66 => [
                'id' => 67,
                'name' => 'ZARAGOZA',
                'region' => null,
                'country_id' => 66,
            ],
            67 => [
                'id' => 68,
                'name' => 'SANTANDER',
                'region' => null,
                'country_id' => 66,
            ],
            68 => [
                'id' => 69,
                'name' => 'SAGRA',
                'region' => null,
                'country_id' => 66,
            ],
            69 => [
                'id' => 70,
                'name' => 'SAGUNTO',
                'region' => null,
                'country_id' => 66,
            ],
        ]);
    }
}
