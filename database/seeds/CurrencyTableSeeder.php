<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('currency')->delete();
        
        \DB::table('currency')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'United Arab Emirates Dirham',
                'alphacode' => 'USDAFN',
                'rates' => 71.300003,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:09',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Afghan Afghani',
                'alphacode' => 'USDALL',
                'rates' => 108.249944,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:09',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Albanian Lek',
                'alphacode' => 'USDAMD',
                'rates' => 482.26001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:09',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Armenian Dram',
                'alphacode' => 'USDANG',
                'rates' => 1.789941,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Netherlands Antillean Guilder',
                'alphacode' => 'USDAOA',
                'rates' => 236.358994,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Angolan Kwanza',
                'alphacode' => 'USDARS',
                'rates' => 24.719999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Argentine Peso',
                'alphacode' => 'USDAUD',
                'rates' => 1.327298,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Australian Dollar',
                'alphacode' => 'USDAWG',
                'rates' => 1.78,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Aruban Florin',
                'alphacode' => 'USDAZN',
                'rates' => 1.6995,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Azerbaijani Manat',
                'alphacode' => 'USDBAM',
                'rates' => 1.685299,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Bosnia-Herzegovina Convertible Mark',
                'alphacode' => 'USDBBD',
                'rates' => 2.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Barbadian Dollar',
                'alphacode' => 'USDBDT',
                'rates' => 83.989998,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Bangladeshi Taka',
                'alphacode' => 'USDBGN',
                'rates' => 1.6797,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Bulgarian Lev',
                'alphacode' => 'USDBHD',
                'rates' => 0.3775,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Bahraini Dinar',
                'alphacode' => 'USDBIF',
                'rates' => 1750.97998,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Burundian Franc',
                'alphacode' => 'USDBMD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Bermudan Dollar',
                'alphacode' => 'USDBND',
                'rates' => 1.3282,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Brunei Dollar',
                'alphacode' => 'USDBOB',
                'rates' => 6.859853,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Bolivian Boliviano',
                'alphacode' => 'USDBRL',
                'rates' => 3.7354,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Brazilian Real',
                'alphacode' => 'USDBSD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Bahamian Dollar',
                'alphacode' => 'USDBTC',
                'rates' => 0.000141,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Bitcoin',
                'alphacode' => 'USDBTN',
                'rates' => 67.750446,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Bhutanese Ngultrum',
                'alphacode' => 'USDBWP',
                'rates' => 9.915603,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Botswanan Pula',
                'alphacode' => 'USDBYN',
                'rates' => 2.01003,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Belarusian Ruble',
                'alphacode' => 'USDBYR',
                'rates' => 19600.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Belize Dollar',
                'alphacode' => 'USDBZD',
                'rates' => 1.997801,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:10',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Canadian Dollar',
                'alphacode' => 'USDCAD',
                'rates' => 1.298399,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Congolese Franc',
                'alphacode' => 'USDCDF',
                'rates' => 1565.50114,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Swiss Franc',
                'alphacode' => 'USDCHF',
                'rates' => 0.99316,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            29 => 
            array (
                'id' => 30,
            'name' => 'Chilean Unit of Account (UF)',
                'alphacode' => 'USDCLF',
                'rates' => 0.02282,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Chilean Peso',
                'alphacode' => 'USDCLP',
                'rates' => 624.190002,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Chinese Yuan',
                'alphacode' => 'USDCNY',
                'rates' => 6.40497,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Colombian Peso',
                'alphacode' => 'USDCOP',
                'rates' => 2874.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Costa Rican Colón',
                'alphacode' => 'USDCRC',
                'rates' => 562.497886,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Cuban Convertible Peso',
                'alphacode' => 'USDCUC',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Cuban Peso',
                'alphacode' => 'USDCUP',
                'rates' => 26.5,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Cape Verdean Escudo',
                'alphacode' => 'USDCVE',
                'rates' => 94.879997,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Czech Republic Koruna',
                'alphacode' => 'USDCZK',
                'rates' => 22.135996,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Djiboutian Franc',
                'alphacode' => 'USDDJF',
                'rates' => 177.502327,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Danish Krone',
                'alphacode' => 'USDDKK',
                'rates' => 6.40812,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Dominican Peso',
                'alphacode' => 'USDDOP',
                'rates' => 49.498607,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Algerian Dinar',
                'alphacode' => 'USDDZD',
                'rates' => 116.438004,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:11',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Egyptian Pound',
                'alphacode' => 'USDEGP',
                'rates' => 17.860001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Eritrean Nakfa',
                'alphacode' => 'USDERN',
                'rates' => 14.990058,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Ethiopian Birr',
                'alphacode' => 'USDETB',
                'rates' => 27.219999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'Euro',
                'alphacode' => 'USDEUR',
                'rates' => 0.860203,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'Fijian Dollar',
                'alphacode' => 'USDFJD',
                'rates' => 2.053796,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'Falkland Islands Pound',
                'alphacode' => 'USDFKP',
                'rates' => 0.750698,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'British Pound Sterling',
                'alphacode' => 'USDGBP',
                'rates' => 0.75129,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'Georgian Lari',
                'alphacode' => 'USDGEL',
                'rates' => 2.456297,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'Guernsey Pound',
                'alphacode' => 'USDGGP',
                'rates' => 0.751254,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'Ghanaian Cedi',
                'alphacode' => 'USDGHS',
                'rates' => 4.658504,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'Gibraltar Pound',
                'alphacode' => 'USDGIP',
                'rates' => 0.75101,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'Gambian Dalasi',
                'alphacode' => 'USDGMD',
                'rates' => 46.810001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'Guinean Franc',
                'alphacode' => 'USDGNF',
                'rates' => 8952.000109,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'Guatemalan Quetzal',
                'alphacode' => 'USDGTQ',
                'rates' => 7.33601,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'Guyanaese Dollar',
                'alphacode' => 'USDGYD',
                'rates' => 207.479996,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:12',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'Hong Kong Dollar',
                'alphacode' => 'USDHKD',
                'rates' => 7.84493,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'Honduran Lempira',
                'alphacode' => 'USDHNL',
                'rates' => 23.820999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'Croatian Kuna',
                'alphacode' => 'USDHRK',
                'rates' => 6.358702,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'Haitian Gourde',
                'alphacode' => 'USDHTG',
                'rates' => 64.540001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'Hungarian Forint',
                'alphacode' => 'USDHUF',
                'rates' => 273.890015,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'Indonesian Rupiah',
                'alphacode' => 'USDIDR',
                'rates' => 13985.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'Israeli New Sheqel',
                'alphacode' => 'USDILS',
                'rates' => 3.569101,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'Manx pound',
                'alphacode' => 'USDIMP',
                'rates' => 0.751254,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'Indian Rupee',
                'alphacode' => 'USDINR',
                'rates' => 67.394997,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'Iraqi Dinar',
                'alphacode' => 'USDIQD',
                'rates' => 1184.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            67 => 
            array (
                'id' => 68,
                'name' => 'Iranian Rial',
                'alphacode' => 'USDIRR',
                'rates' => 42124.999855,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'Icelandic Króna',
                'alphacode' => 'USDISK',
                'rates' => 105.669998,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'Jersey Pound',
                'alphacode' => 'USDJEP',
                'rates' => 0.751254,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            70 => 
            array (
                'id' => 71,
                'name' => 'Jamaican Dollar',
                'alphacode' => 'USDJMD',
                'rates' => 124.749788,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'Jordanian Dinar',
                'alphacode' => 'USDJOD',
                'rates' => 0.708496,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            72 => 
            array (
                'id' => 73,
                'name' => 'Japanese Yen',
                'alphacode' => 'USDJPY',
                'rates' => 109.081001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            73 => 
            array (
                'id' => 74,
                'name' => 'Kenyan Shilling',
                'alphacode' => 'USDKES',
                'rates' => 101.300003,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            74 => 
            array (
                'id' => 75,
                'name' => 'Kyrgystani Som',
                'alphacode' => 'USDKGS',
                'rates' => 68.210701,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            75 => 
            array (
                'id' => 76,
                'name' => 'Cambodian Riel',
                'alphacode' => 'USDKHR',
                'rates' => 4067.00032,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            76 => 
            array (
                'id' => 77,
                'name' => 'Comorian Franc',
                'alphacode' => 'USDKMF',
                'rates' => 418.720001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            77 => 
            array (
                'id' => 78,
                'name' => 'North Korean Won',
                'alphacode' => 'USDKPW',
                'rates' => 899.99975,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            78 => 
            array (
                'id' => 79,
                'name' => 'South Korean Won',
                'alphacode' => 'USDKRW',
                'rates' => 1074.650024,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            79 => 
            array (
                'id' => 80,
                'name' => 'Kuwaiti Dinar',
                'alphacode' => 'USDKWD',
                'rates' => 0.302031,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            80 => 
            array (
                'id' => 81,
                'name' => 'Cayman Islands Dollar',
                'alphacode' => 'USDKYD',
                'rates' => 0.820005,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            81 => 
            array (
                'id' => 82,
                'name' => 'Kazakhstani Tenge',
                'alphacode' => 'USDKZT',
                'rates' => 329.609985,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:13',
            ),
            82 => 
            array (
                'id' => 83,
                'name' => 'Laotian Kip',
                'alphacode' => 'USDLAK',
                'rates' => 8334.000262,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            83 => 
            array (
                'id' => 84,
                'name' => 'Lebanese Pound',
                'alphacode' => 'USDLBP',
                'rates' => 1505.000157,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            84 => 
            array (
                'id' => 85,
                'name' => 'Sri Lankan Rupee',
                'alphacode' => 'USDLKR',
                'rates' => 157.850006,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            85 => 
            array (
                'id' => 86,
                'name' => 'Liberian Dollar',
                'alphacode' => 'USDLRD',
                'rates' => 137.300003,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            86 => 
            array (
                'id' => 87,
                'name' => 'Lesotho Loti',
                'alphacode' => 'USDLSL',
                'rates' => 12.49024,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            87 => 
            array (
                'id' => 88,
                'name' => 'Lithuanian Litas',
                'alphacode' => 'USDLTL',
                'rates' => 3.048703,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            88 => 
            array (
                'id' => 89,
                'name' => 'Latvian Lats',
                'alphacode' => 'USDLVL',
                'rates' => 0.62055,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            89 => 
            array (
                'id' => 90,
                'name' => 'Libyan Dinar',
                'alphacode' => 'USDLYD',
                'rates' => 1.359501,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            90 => 
            array (
                'id' => 91,
                'name' => 'Moroccan Dirham',
                'alphacode' => 'USDMAD',
                'rates' => 9.513988,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            91 => 
            array (
                'id' => 92,
                'name' => 'Moldovan Leu',
                'alphacode' => 'USDMDL',
                'rates' => 16.822001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            92 => 
            array (
                'id' => 93,
                'name' => 'Malagasy Ariary',
                'alphacode' => 'USDMGA',
                'rates' => 3224.999509,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            93 => 
            array (
                'id' => 94,
                'name' => 'Macedonian Denar',
                'alphacode' => 'USDMKD',
                'rates' => 52.700001,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            94 => 
            array (
                'id' => 95,
                'name' => 'Myanma Kyat',
                'alphacode' => 'USDMMK',
                'rates' => 1354.999946,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            95 => 
            array (
                'id' => 96,
                'name' => 'Mongolian Tugrik',
                'alphacode' => 'USDMNT',
                'rates' => 2400.999989,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:14',
            ),
            96 => 
            array (
                'id' => 97,
                'name' => 'Macanese Pataca',
                'alphacode' => 'USDMOP',
                'rates' => 8.079811,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            97 => 
            array (
                'id' => 98,
                'name' => 'Mauritanian Ouguiya',
                'alphacode' => 'USDMRO',
                'rates' => 354.000351,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            98 => 
            array (
                'id' => 99,
                'name' => 'Mauritian Rupee',
                'alphacode' => 'USDMUR',
                'rates' => 34.400002,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            99 => 
            array (
                'id' => 100,
                'name' => 'Maldivian Rufiyaa',
                'alphacode' => 'USDMVR',
                'rates' => 15.570227,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            100 => 
            array (
                'id' => 101,
                'name' => 'Malawian Kwacha',
                'alphacode' => 'USDMWK',
                'rates' => 713.450012,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            101 => 
            array (
                'id' => 102,
                'name' => 'Mexican Peso',
                'alphacode' => 'USDMXN',
                'rates' => 19.615299,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            102 => 
            array (
                'id' => 103,
                'name' => 'Malaysian Ringgit',
                'alphacode' => 'USDMYR',
                'rates' => 3.980308,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            103 => 
            array (
                'id' => 104,
                'name' => 'Mozambican Metical',
                'alphacode' => 'USDMZN',
                'rates' => 60.349998,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            104 => 
            array (
                'id' => 105,
                'name' => 'Namibian Dollar',
                'alphacode' => 'USDNAD',
                'rates' => 12.443964,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            105 => 
            array (
                'id' => 106,
                'name' => 'Nigerian Naira',
                'alphacode' => 'USDNGN',
                'rates' => 358.999858,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            106 => 
            array (
                'id' => 107,
                'name' => 'Nicaraguan Córdoba',
                'alphacode' => 'USDNIO',
                'rates' => 31.405596,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            107 => 
            array (
                'id' => 108,
                'name' => 'Norwegian Krone',
                'alphacode' => 'USDNOK',
                'rates' => 8.20066,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            108 => 
            array (
                'id' => 109,
                'name' => 'Nepalese Rupee',
                'alphacode' => 'USDNPR',
                'rates' => 108.150002,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            109 => 
            array (
                'id' => 110,
                'name' => 'New Zealand Dollar',
                'alphacode' => 'USDNZD',
                'rates' => 1.442301,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            110 => 
            array (
                'id' => 111,
                'name' => 'Omani Rial',
                'alphacode' => 'USDOMR',
                'rates' => 0.384699,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            111 => 
            array (
                'id' => 112,
                'name' => 'Panamanian Balboa',
                'alphacode' => 'USDPAB',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            112 => 
            array (
                'id' => 113,
                'name' => 'Peruvian Nuevo Sol',
                'alphacode' => 'USDPEN',
                'rates' => 3.2745,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            113 => 
            array (
                'id' => 114,
                'name' => 'Papua New Guinean Kina',
                'alphacode' => 'USDPGK',
                'rates' => 3.260096,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            114 => 
            array (
                'id' => 115,
                'name' => 'Philippine Peso',
                'alphacode' => 'USDPHP',
                'rates' => 52.529999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            115 => 
            array (
                'id' => 116,
                'name' => 'Pakistani Rupee',
                'alphacode' => 'USDPKR',
                'rates' => 115.497444,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            116 => 
            array (
                'id' => 117,
                'name' => 'Polish Zloty',
                'alphacode' => 'USDPLN',
                'rates' => 3.700801,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            117 => 
            array (
                'id' => 118,
                'name' => 'Paraguayan Guarani',
                'alphacode' => 'USDPYG',
                'rates' => 5693.899902,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            118 => 
            array (
                'id' => 119,
                'name' => 'Qatari Rial',
                'alphacode' => 'USDQAR',
                'rates' => 3.639799,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:15',
            ),
            119 => 
            array (
                'id' => 120,
                'name' => 'Romanian Leu',
                'alphacode' => 'USDRON',
                'rates' => 3.988985,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:16',
            ),
            120 => 
            array (
                'id' => 121,
                'name' => 'Serbian Dinar',
                'alphacode' => 'USDRSD',
                'rates' => 100.495499,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:16',
            ),
            121 => 
            array (
                'id' => 122,
                'name' => 'Russian Ruble',
                'alphacode' => 'USDRUB',
                'rates' => 62.294498,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:16',
            ),
            122 => 
            array (
                'id' => 123,
                'name' => 'Rwandan Franc',
                'alphacode' => 'USDRWF',
                'rates' => 846.409973,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            123 => 
            array (
                'id' => 124,
                'name' => 'Saudi Riyal',
                'alphacode' => 'USDSAR',
                'rates' => 3.750101,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            124 => 
            array (
                'id' => 125,
                'name' => 'Solomon Islands Dollar',
                'alphacode' => 'USDSBD',
                'rates' => 7.871503,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            125 => 
            array (
                'id' => 126,
                'name' => 'Seychellois Rupee',
                'alphacode' => 'USDSCR',
                'rates' => 13.429664,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            126 => 
            array (
                'id' => 127,
                'name' => 'Sudanese Pound',
                'alphacode' => 'USDSDG',
                'rates' => 17.955202,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            127 => 
            array (
                'id' => 128,
                'name' => 'Swedish Krona',
                'alphacode' => 'USDSEK',
                'rates' => 8.82582,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:17',
            ),
            128 => 
            array (
                'id' => 129,
                'name' => 'Singapore Dollar',
                'alphacode' => 'USDSGD',
                'rates' => 1.34328,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            129 => 
            array (
                'id' => 130,
                'name' => 'Saint Helena Pound',
                'alphacode' => 'USDSHP',
                'rates' => 0.750963,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            130 => 
            array (
                'id' => 131,
                'name' => 'Sierra Leonean Leone',
                'alphacode' => 'USDSLL',
                'rates' => 7850.000231,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            131 => 
            array (
                'id' => 132,
                'name' => 'Somali Shilling',
                'alphacode' => 'USDSOS',
                'rates' => 562.000199,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            132 => 
            array (
                'id' => 133,
                'name' => 'Surinamese Dollar',
                'alphacode' => 'USDSRD',
                'rates' => 7.430066,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            133 => 
            array (
                'id' => 134,
                'name' => 'São Tomé and Príncipe Dobra',
                'alphacode' => 'USDSTD',
                'rates' => 21080.699219,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            134 => 
            array (
                'id' => 135,
                'name' => 'Salvadoran Colón',
                'alphacode' => 'USDSVC',
                'rates' => 8.750475,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            135 => 
            array (
                'id' => 136,
                'name' => 'Syrian Pound',
                'alphacode' => 'USDSYP',
                'rates' => 514.97998,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            136 => 
            array (
                'id' => 137,
                'name' => 'Swazi Lilangeni',
                'alphacode' => 'USDSZL',
                'rates' => 12.4561,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            137 => 
            array (
                'id' => 138,
                'name' => 'Thai Baht',
                'alphacode' => 'USDTHB',
                'rates' => 32.049999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            138 => 
            array (
                'id' => 139,
                'name' => 'Tajikistani Somoni',
                'alphacode' => 'USDTJS',
                'rates' => 8.984401,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            139 => 
            array (
                'id' => 140,
                'name' => 'Turkmenistani Manat',
                'alphacode' => 'USDTMT',
                'rates' => 3.41,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            140 => 
            array (
                'id' => 141,
                'name' => 'Tunisian Dinar',
                'alphacode' => 'USDTND',
                'rates' => 2.557506,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            141 => 
            array (
                'id' => 142,
                'name' => 'Tongan Paʻanga',
                'alphacode' => 'USDTOP',
                'rates' => 2.318797,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            142 => 
            array (
                'id' => 143,
                'name' => 'Turkish Lira',
                'alphacode' => 'USDTRY',
                'rates' => 4.583599,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            143 => 
            array (
                'id' => 144,
                'name' => 'Trinidad and Tobago Dollar',
                'alphacode' => 'USDTTD',
                'rates' => 6.649495,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            144 => 
            array (
                'id' => 145,
                'name' => 'New Taiwan Dollar',
                'alphacode' => 'USDTWD',
                'rates' => 29.948999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            145 => 
            array (
                'id' => 146,
                'name' => 'Tanzanian Shilling',
                'alphacode' => 'USDTZS',
                'rates' => 2274.999992,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            146 => 
            array (
                'id' => 147,
                'name' => 'Ukrainian Hryvnia',
                'alphacode' => 'USDUAH',
                'rates' => 26.129999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            147 => 
            array (
                'id' => 148,
                'name' => 'Ugandan Shilling',
                'alphacode' => 'USDUGX',
                'rates' => 3746.999948,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            148 => 
            array (
                'id' => 149,
                'name' => 'United States Dollar',
                'alphacode' => 'USDUSD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            149 => 
            array (
                'id' => 150,
                'name' => 'Uruguayan Peso',
                'alphacode' => 'USDUYU',
                'rates' => 31.159848,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            150 => 
            array (
                'id' => 151,
                'name' => 'Uzbekistan Som',
                'alphacode' => 'USDUZS',
                'rates' => 7974.99981,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            151 => 
            array (
                'id' => 152,
                'name' => 'Venezuelan Bolívar Fuerte',
                'alphacode' => 'USDVEF',
                'rates' => 79800.000193,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            152 => 
            array (
                'id' => 153,
                'name' => 'Vietnamese Dong',
                'alphacode' => 'USDVND',
                'rates' => 22813.0,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:18',
            ),
            153 => 
            array (
                'id' => 154,
                'name' => 'Vanuatu Vatu',
                'alphacode' => 'USDVUV',
                'rates' => 109.529999,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            154 => 
            array (
                'id' => 155,
                'name' => 'Samoan Tala',
                'alphacode' => 'USDWST',
                'rates' => 2.585503,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            155 => 
            array (
                'id' => 156,
                'name' => 'CFA Franc BEAC',
                'alphacode' => 'USDXAF',
                'rates' => 563.960022,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            156 => 
            array (
                'id' => 157,
            'name' => 'Silver (troy ounce)',
                'alphacode' => 'USDXAG',
                'rates' => 0.060663,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            157 => 
            array (
                'id' => 158,
            'name' => 'Gold (troy ounce)',
                'alphacode' => 'USDXAU',
                'rates' => 0.00077,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            158 => 
            array (
                'id' => 159,
                'name' => 'East Caribbean Dollar',
                'alphacode' => 'USDXCD',
                'rates' => 2.702616,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            159 => 
            array (
                'id' => 160,
                'name' => 'Special Drawing Rights',
                'alphacode' => 'USDXDR',
                'rates' => 0.704722,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            160 => 
            array (
                'id' => 161,
                'name' => 'CFA Franc BCEAO',
                'alphacode' => 'USDXOF',
                'rates' => 563.960022,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            161 => 
            array (
                'id' => 162,
                'name' => 'CFP Franc',
                'alphacode' => 'USDXPF',
                'rates' => 102.674044,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            162 => 
            array (
                'id' => 163,
                'name' => 'Yemeni Rial',
                'alphacode' => 'USDYER',
                'rates' => 249.850006,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            163 => 
            array (
                'id' => 164,
                'name' => 'South African Rand',
                'alphacode' => 'USDZAR',
                'rates' => 12.459698,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            164 => 
            array (
                'id' => 165,
            'name' => 'Zambian Kwacha (pre-2013)',
                'alphacode' => 'USDZMK',
                'rates' => 9001.189986,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            165 => 
            array (
                'id' => 166,
                'name' => 'Zambian Kwacha',
                'alphacode' => 'USDZMW',
                'rates' => 10.329731,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
            166 => 
            array (
                'id' => 167,
                'name' => 'Zimbabwean Dollar',
                'alphacode' => 'USDZWL',
                'rates' => 322.355011,
                'created_at' => NULL,
                'updated_at' => '2018-05-29 02:24:19',
            ),
        ));
        
        
    }
}