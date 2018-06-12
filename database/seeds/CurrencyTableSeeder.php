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
                'alphacode' => 'AED',
                'api_code' => 'USDAED',
                'rates' => 3.672703,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:50',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Afghan Afghani',
                'alphacode' => 'AFN',
                'api_code' => 'USDAFN',
                'rates' => 71.150002,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:35',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Albanian Lek',
                'alphacode' => 'ALL',
                'api_code' => 'USDALL',
                'rates' => 109.559998,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:35',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Armenian Dram',
                'alphacode' => 'AMD',
                'api_code' => 'USDAMD',
                'rates' => 482.640015,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:35',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Netherlands Antillean Guilder',
                'alphacode' => 'ANG',
                'api_code' => 'USDANG',
                'rates' => 1.789834,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Angolan Kwanza',
                'alphacode' => 'AOA',
                'api_code' => 'USDAOA',
                'rates' => 238.889008,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Argentine Peso',
                'alphacode' => 'ARS',
                'api_code' => 'USDARS',
                'rates' => 25.649769,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Australian Dollar',
                'alphacode' => 'AUD',
                'api_code' => 'USDAUD',
                'rates' => 1.313802,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Aruban Florin',
                'alphacode' => 'AWG',
                'api_code' => 'USDAWG',
                'rates' => 1.78,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Azerbaijani Manat',
                'alphacode' => 'AZN',
                'api_code' => 'USDAZN',
                'rates' => 1.699497,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Bosnia-Herzegovina Convertible Mark',
                'alphacode' => 'BAM',
                'api_code' => 'USDBAM',
                'rates' => 1.662094,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Barbadian Dollar',
                'alphacode' => 'BBD',
                'api_code' => 'USDBBD',
                'rates' => 2.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Bangladeshi Taka',
                'alphacode' => 'BDT',
                'api_code' => 'USDBDT',
                'rates' => 84.290001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Bulgarian Lev',
                'alphacode' => 'BGN',
                'api_code' => 'USDBGN',
                'rates' => 1.658897,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Bahraini Dinar',
                'alphacode' => 'BHD',
                'api_code' => 'USDBHD',
                'rates' => 0.377501,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Burundian Franc',
                'alphacode' => 'BIF',
                'api_code' => 'USDBIF',
                'rates' => 1750.97998,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Bermudan Dollar',
                'alphacode' => 'BMD',
                'api_code' => 'USDBMD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Brunei Dollar',
                'alphacode' => 'BND',
                'api_code' => 'USDBND',
                'rates' => 1.320798,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Bolivian Boliviano',
                'alphacode' => 'BOB',
                'api_code' => 'USDBOB',
                'rates' => 6.860233,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Brazilian Real',
                'alphacode' => 'BRL',
                'api_code' => 'USDBRL',
                'rates' => 3.713003,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Bahamian Dollar',
                'alphacode' => 'BSD',
                'api_code' => 'USDBSD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Bitcoin',
                'alphacode' => 'BTC',
                'api_code' => 'USDBTC',
                'rates' => 0.000148,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Bhutanese Ngultrum',
                'alphacode' => 'BTN',
                'api_code' => 'USDBTN',
                'rates' => 67.750066,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Botswanan Pula',
                'alphacode' => 'BWP',
                'api_code' => 'USDBWP',
                'rates' => 10.106202,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:29:36',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Belarusian Ruble',
                'alphacode' => 'BYR',
                'api_code' => 'USDBYR',
                'rates' => 19600.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:43',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Belize Dollar',
                'alphacode' => 'BZD',
                'api_code' => 'USDBZD',
                'rates' => 1.997805,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:43',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Canadian Dollar',
                'alphacode' => 'CAD',
                'api_code' => 'USDCAD',
                'rates' => 1.297404,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:43',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Congolese Franc',
                'alphacode' => 'CDF',
                'api_code' => 'USDCDF',
                'rates' => 1565.498699,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:43',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Swiss Franc',
                'alphacode' => 'CHF',
                'api_code' => 'USDCHF',
                'rates' => 0.98536,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            29 => 
            array (
                'id' => 30,
            'name' => 'Chilean Unit of Account (UF)',
                'alphacode' => 'CLF',
                'api_code' => 'USDCLF',
                'rates' => 0.02312,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Chilean Peso',
                'alphacode' => 'CLP',
                'api_code' => 'USDCLP',
                'rates' => 632.890015,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Chinese Yuan',
                'alphacode' => 'CNY',
                'api_code' => 'USDCNY',
                'rates' => 6.3999,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Colombian Peso',
                'alphacode' => 'COP',
                'api_code' => 'USDCOP',
                'rates' => 2861.800049,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Costa Rican Colón',
                'alphacode' => 'CRC',
                'api_code' => 'USDCRC',
                'rates' => 565.299988,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Cuban Convertible Peso',
                'alphacode' => 'CUC',
                'api_code' => 'USDCUC',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Cuban Peso',
                'alphacode' => 'CUP',
                'api_code' => 'USDCUP',
                'rates' => 26.5,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Cape Verdean Escudo',
                'alphacode' => 'CVE',
                'api_code' => 'USDCVE',
                'rates' => 93.589996,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Czech Republic Koruna',
                'alphacode' => 'CZK',
                'api_code' => 'USDCZK',
                'rates' => 21.759701,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Djiboutian Franc',
                'alphacode' => 'DJF',
                'api_code' => 'USDDJF',
                'rates' => 177.490818,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Danish Krone',
                'alphacode' => 'DKK',
                'api_code' => 'USDDKK',
                'rates' => 6.320198,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Dominican Peso',
                'alphacode' => 'DOP',
                'api_code' => 'USDDOP',
                'rates' => 49.520032,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Algerian Dinar',
                'alphacode' => 'DZD',
                'api_code' => 'USDDZD',
                'rates' => 115.968002,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Egyptian Pound',
                'alphacode' => 'EGP',
                'api_code' => 'USDEGP',
                'rates' => 17.780001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Eritrean Nakfa',
                'alphacode' => 'ERN',
                'api_code' => 'USDERN',
                'rates' => 14.989545,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Ethiopian Birr',
                'alphacode' => 'ETB',
                'api_code' => 'USDETB',
                'rates' => 27.200001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'Euro',
                'alphacode' => 'EUR',
                'api_code' => 'USDEUR',
                'rates' => 0.848303,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'Fijian Dollar',
                'alphacode' => 'FJD',
                'api_code' => 'USDFJD',
                'rates' => 2.053984,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'Falkland Islands Pound',
                'alphacode' => 'FKP',
                'api_code' => 'USDFKP',
                'rates' => 0.7476,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'British Pound Sterling',
                'alphacode' => 'GBP',
                'api_code' => 'USDGBP',
                'rates' => 0.74766,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'Georgian Lari',
                'alphacode' => 'GEL',
                'api_code' => 'USDGEL',
                'rates' => 2.443798,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'Guernsey Pound',
                'alphacode' => 'GGP',
                'api_code' => 'USDGGP',
                'rates' => 0.747681,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'Ghanaian Cedi',
                'alphacode' => 'GHS',
                'api_code' => 'USDGHS',
                'rates' => 4.698501,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'Gibraltar Pound',
                'alphacode' => 'GIP',
                'api_code' => 'USDGIP',
                'rates' => 0.7479,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:44',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'Gambian Dalasi',
                'alphacode' => 'GMD',
                'api_code' => 'USDGMD',
                'rates' => 46.869999,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'Guinean Franc',
                'alphacode' => 'GNF',
                'api_code' => 'USDGNF',
                'rates' => 8994.999975,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'Guatemalan Quetzal',
                'alphacode' => 'GTQ',
                'api_code' => 'USDGTQ',
                'rates' => 7.335998,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'Guyanaese Dollar',
                'alphacode' => 'GYD',
                'api_code' => 'USDGYD',
                'rates' => 207.059998,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'Hong Kong Dollar',
                'alphacode' => 'HKD',
                'api_code' => 'USDHKD',
                'rates' => 7.844895,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'Honduran Lempira',
                'alphacode' => 'HNL',
                'api_code' => 'USDHNL',
                'rates' => 23.874001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'Croatian Kuna',
                'alphacode' => 'HRK',
                'api_code' => 'USDHRK',
                'rates' => 6.255905,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'Haitian Gourde',
                'alphacode' => 'HTG',
                'api_code' => 'USDHTG',
                'rates' => 66.050003,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'Hungarian Forint',
                'alphacode' => 'HUF',
                'api_code' => 'USDHUF',
                'rates' => 272.480011,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'Indonesian Rupiah',
                'alphacode' => 'IDR',
                'api_code' => 'USDIDR',
                'rates' => 13925.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'Israeli New Sheqel',
                'alphacode' => 'ILS',
                'api_code' => 'USDILS',
                'rates' => 3.570299,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'Manx pound',
                'alphacode' => 'IMP',
                'api_code' => 'USDIMP',
                'rates' => 0.747681,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'Indian Rupee',
                'alphacode' => 'INR',
                'api_code' => 'USDINR',
                'rates' => 67.472397,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'Iraqi Dinar',
                'alphacode' => 'IQD',
                'api_code' => 'USDIQD',
                'rates' => 1184.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            67 => 
            array (
                'id' => 68,
                'name' => 'Iranian Rial',
                'alphacode' => 'IRR',
                'api_code' => 'USDIRR',
                'rates' => 42229.999742,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'Icelandic Króna',
                'alphacode' => 'ISK',
                'api_code' => 'USDISK',
                'rates' => 105.610001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'Jersey Pound',
                'alphacode' => 'JEP',
                'api_code' => 'USDJEP',
                'rates' => 0.747681,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            70 => 
            array (
                'id' => 71,
                'name' => 'Jamaican Dollar',
                'alphacode' => 'JMD',
                'api_code' => 'USDJMD',
                'rates' => 127.620003,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'Jordanian Dinar',
                'alphacode' => 'JOD',
                'api_code' => 'USDJOD',
                'rates' => 0.708502,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            72 => 
            array (
                'id' => 73,
                'name' => 'Japanese Yen',
                'alphacode' => 'JPY',
                'api_code' => 'USDJPY',
                'rates' => 110.077003,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            73 => 
            array (
                'id' => 74,
                'name' => 'Kenyan Shilling',
                'alphacode' => 'KES',
                'api_code' => 'USDKES',
                'rates' => 100.750211,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            74 => 
            array (
                'id' => 75,
                'name' => 'Kyrgystani Som',
                'alphacode' => 'KGS',
                'api_code' => 'USDKGS',
                'rates' => 68.427803,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            75 => 
            array (
                'id' => 76,
                'name' => 'Cambodian Riel',
                'alphacode' => 'KHR',
                'api_code' => 'USDKHR',
                'rates' => 3997.485115,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            76 => 
            array (
                'id' => 77,
                'name' => 'Comorian Franc',
                'alphacode' => 'KMF',
                'api_code' => 'USDKMF',
                'rates' => 417.880005,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            77 => 
            array (
                'id' => 78,
                'name' => 'North Korean Won',
                'alphacode' => 'KPW',
                'api_code' => 'USDKPW',
                'rates' => 900.000301,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            78 => 
            array (
                'id' => 79,
                'name' => 'South Korean Won',
                'alphacode' => 'KRW',
                'api_code' => 'USDKRW',
                'rates' => 1074.959961,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            79 => 
            array (
                'id' => 80,
                'name' => 'Kuwaiti Dinar',
                'alphacode' => 'KWD',
                'api_code' => 'USDKWD',
                'rates' => 0.301703,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            80 => 
            array (
                'id' => 81,
                'name' => 'Cayman Islands Dollar',
                'alphacode' => 'KYD',
                'api_code' => 'USDKYD',
                'rates' => 0.820226,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            81 => 
            array (
                'id' => 82,
                'name' => 'Kazakhstani Tenge',
                'alphacode' => 'KZT',
                'api_code' => 'USDKZT',
                'rates' => 333.910004,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            82 => 
            array (
                'id' => 83,
                'name' => 'Laotian Kip',
                'alphacode' => 'LAK',
                'api_code' => 'USDLAK',
                'rates' => 8337.999487,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            83 => 
            array (
                'id' => 84,
                'name' => 'Lebanese Pound',
                'alphacode' => 'LBP',
                'api_code' => 'USDLBP',
                'rates' => 1504.999715,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:45',
            ),
            84 => 
            array (
                'id' => 85,
                'name' => 'Sri Lankan Rupee',
                'alphacode' => 'LKR',
                'api_code' => 'USDLKR',
                'rates' => 159.100006,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            85 => 
            array (
                'id' => 86,
                'name' => 'Liberian Dollar',
                'alphacode' => 'LRD',
                'api_code' => 'USDLRD',
                'rates' => 140.320007,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            86 => 
            array (
                'id' => 87,
                'name' => 'Lesotho Loti',
                'alphacode' => 'LSL',
                'api_code' => 'USDLSL',
                'rates' => 13.089833,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            87 => 
            array (
                'id' => 88,
                'name' => 'Lithuanian Litas',
                'alphacode' => 'LTL',
                'api_code' => 'USDLTL',
                'rates' => 3.048701,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            88 => 
            array (
                'id' => 89,
                'name' => 'Latvian Lats',
                'alphacode' => 'LVL',
                'api_code' => 'USDLVL',
                'rates' => 0.62055,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            89 => 
            array (
                'id' => 90,
                'name' => 'Libyan Dinar',
                'alphacode' => 'LYD',
                'api_code' => 'USDLYD',
                'rates' => 1.35602,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            90 => 
            array (
                'id' => 91,
                'name' => 'Moroccan Dirham',
                'alphacode' => 'MAD',
                'api_code' => 'USDMAD',
                'rates' => 9.435603,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            91 => 
            array (
                'id' => 92,
                'name' => 'Moldovan Leu',
                'alphacode' => 'MDL',
                'api_code' => 'USDMDL',
                'rates' => 16.801001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            92 => 
            array (
                'id' => 93,
                'name' => 'Malagasy Ariary',
                'alphacode' => 'MGA',
                'api_code' => 'USDMGA',
                'rates' => 3270.000024,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            93 => 
            array (
                'id' => 94,
                'name' => 'Macedonian Denar',
                'alphacode' => 'MKD',
                'api_code' => 'USDMKD',
                'rates' => 51.880001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            94 => 
            array (
                'id' => 95,
                'name' => 'Myanma Kyat',
                'alphacode' => 'MMK',
                'api_code' => 'USDMMK',
                'rates' => 1351.999653,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            95 => 
            array (
                'id' => 96,
                'name' => 'Mongolian Tugrik',
                'alphacode' => 'MNT',
                'api_code' => 'USDMNT',
                'rates' => 2405.999819,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            96 => 
            array (
                'id' => 97,
                'name' => 'Macanese Pataca',
                'alphacode' => 'MOP',
                'api_code' => 'USDMOP',
                'rates' => 8.081396,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            97 => 
            array (
                'id' => 98,
                'name' => 'Mauritanian Ouguiya',
                'alphacode' => 'MRO',
                'api_code' => 'USDMRO',
                'rates' => 354.000381,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            98 => 
            array (
                'id' => 99,
                'name' => 'Mauritian Rupee',
                'alphacode' => 'MUR',
                'api_code' => 'USDMUR',
                'rates' => 33.750293,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            99 => 
            array (
                'id' => 100,
                'name' => 'Maldivian Rufiyaa',
                'alphacode' => 'MVR',
                'api_code' => 'USDMVR',
                'rates' => 15.569677,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            100 => 
            array (
                'id' => 101,
                'name' => 'Malawian Kwacha',
                'alphacode' => 'MWK',
                'api_code' => 'USDMWK',
                'rates' => 713.440002,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:46',
            ),
            101 => 
            array (
                'id' => 102,
                'name' => 'Mexican Peso',
                'alphacode' => 'MXN',
                'api_code' => 'USDMXN',
                'rates' => 20.538041,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            102 => 
            array (
                'id' => 103,
                'name' => 'Malaysian Ringgit',
                'alphacode' => 'MYR',
                'api_code' => 'USDMYR',
                'rates' => 3.986022,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            103 => 
            array (
                'id' => 104,
                'name' => 'Mozambican Metical',
                'alphacode' => 'MZN',
                'api_code' => 'USDMZN',
                'rates' => 58.799999,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            104 => 
            array (
                'id' => 105,
                'name' => 'Namibian Dollar',
                'alphacode' => 'NAD',
                'api_code' => 'USDNAD',
                'rates' => 13.13897,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            105 => 
            array (
                'id' => 106,
                'name' => 'Nigerian Naira',
                'alphacode' => 'NGN',
                'api_code' => 'USDNGN',
                'rates' => 359.000245,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            106 => 
            array (
                'id' => 107,
                'name' => 'Nicaraguan Córdoba',
                'alphacode' => 'NIO',
                'api_code' => 'USDNIO',
                'rates' => 31.464499,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            107 => 
            array (
                'id' => 108,
                'name' => 'Norwegian Krone',
                'alphacode' => 'NOK',
                'api_code' => 'USDNOK',
                'rates' => 8.04174,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            108 => 
            array (
                'id' => 109,
                'name' => 'Nepalese Rupee',
                'alphacode' => 'NPR',
                'api_code' => 'USDNPR',
                'rates' => 107.709999,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            109 => 
            array (
                'id' => 110,
                'name' => 'New Zealand Dollar',
                'alphacode' => 'NZD',
                'api_code' => 'USDNZD',
                'rates' => 1.422903,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            110 => 
            array (
                'id' => 111,
                'name' => 'Omani Rial',
                'alphacode' => 'OMR',
                'api_code' => 'USDOMR',
                'rates' => 0.384902,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            111 => 
            array (
                'id' => 112,
                'name' => 'Panamanian Balboa',
                'alphacode' => 'PAB',
                'api_code' => 'USDPAB',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            112 => 
            array (
                'id' => 113,
                'name' => 'Peruvian Nuevo Sol',
                'alphacode' => 'PEN',
                'api_code' => 'USDPEN',
                'rates' => 3.266502,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            113 => 
            array (
                'id' => 114,
                'name' => 'Papua New Guinean Kina',
                'alphacode' => 'PGK',
                'api_code' => 'USDPGK',
                'rates' => 3.260099,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            114 => 
            array (
                'id' => 115,
                'name' => 'Philippine Peso',
                'alphacode' => 'PHP',
                'api_code' => 'USDPHP',
                'rates' => 53.130001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            115 => 
            array (
                'id' => 116,
                'name' => 'Pakistani Rupee',
                'alphacode' => 'PKR',
                'api_code' => 'USDPKR',
                'rates' => 116.540001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            116 => 
            array (
                'id' => 117,
                'name' => 'Polish Zloty',
                'alphacode' => 'PLN',
                'api_code' => 'USDPLN',
                'rates' => 3.618901,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:47',
            ),
            117 => 
            array (
                'id' => 118,
                'name' => 'Paraguayan Guarani',
                'alphacode' => 'PYG',
                'api_code' => 'USDPYG',
                'rates' => 5648.899902,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            118 => 
            array (
                'id' => 119,
                'name' => 'Qatari Rial',
                'alphacode' => 'QAR',
                'api_code' => 'USDQAR',
                'rates' => 3.639803,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            119 => 
            array (
                'id' => 120,
                'name' => 'Romanian Leu',
                'alphacode' => 'RON',
                'api_code' => 'USDRON',
                'rates' => 3.9523,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            120 => 
            array (
                'id' => 121,
                'name' => 'Serbian Dinar',
                'alphacode' => 'RSD',
                'api_code' => 'USDRSD',
                'rates' => 99.840797,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            121 => 
            array (
                'id' => 122,
                'name' => 'Russian Ruble',
                'alphacode' => 'RUB',
                'api_code' => 'USDRUB',
                'rates' => 62.852095,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            122 => 
            array (
                'id' => 123,
                'name' => 'Rwandan Franc',
                'alphacode' => 'RWF',
                'api_code' => 'USDRWF',
                'rates' => 849.429993,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            123 => 
            array (
                'id' => 124,
                'name' => 'Saudi Riyal',
                'alphacode' => 'SAR',
                'api_code' => 'USDSAR',
                'rates' => 3.749902,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            124 => 
            array (
                'id' => 125,
                'name' => 'Solomon Islands Dollar',
                'alphacode' => 'SBD',
                'api_code' => 'USDSBD',
                'rates' => 7.902594,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            125 => 
            array (
                'id' => 126,
                'name' => 'Seychellois Rupee',
                'alphacode' => 'SCR',
                'api_code' => 'USDSCR',
                'rates' => 13.429969,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            126 => 
            array (
                'id' => 127,
                'name' => 'Sudanese Pound',
                'alphacode' => 'SDG',
                'api_code' => 'USDSDG',
                'rates' => 17.955202,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            127 => 
            array (
                'id' => 128,
                'name' => 'Swedish Krona',
                'alphacode' => 'SEK',
                'api_code' => 'USDSEK',
                'rates' => 8.668499,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:48',
            ),
            128 => 
            array (
                'id' => 129,
                'name' => 'Singapore Dollar',
                'alphacode' => 'SGD',
                'api_code' => 'USDSGD',
                'rates' => 1.33486,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:49',
            ),
            129 => 
            array (
                'id' => 130,
                'name' => 'Saint Helena Pound',
                'alphacode' => 'SHP',
                'api_code' => 'USDSHP',
                'rates' => 0.747901,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:49',
            ),
            130 => 
            array (
                'id' => 131,
                'name' => 'Sierra Leonean Leone',
                'alphacode' => 'SLL',
                'api_code' => 'USDSLL',
                'rates' => 7849.999633,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:49',
            ),
            131 => 
            array (
                'id' => 132,
                'name' => 'Somali Shilling',
                'alphacode' => 'SOS',
                'api_code' => 'USDSOS',
                'rates' => 562.999945,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:49',
            ),
            132 => 
            array (
                'id' => 133,
                'name' => 'Surinamese Dollar',
                'alphacode' => 'SRD',
                'api_code' => 'USDSRD',
                'rates' => 7.429667,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            133 => 
            array (
                'id' => 134,
                'name' => 'São Tomé and Príncipe Dobra',
                'alphacode' => 'STD',
                'api_code' => 'USDSTD',
                'rates' => 20794.400391,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            134 => 
            array (
                'id' => 135,
                'name' => 'Salvadoran Colón',
                'alphacode' => 'SVC',
                'api_code' => 'USDSVC',
                'rates' => 8.749909,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            135 => 
            array (
                'id' => 136,
                'name' => 'Syrian Pound',
                'alphacode' => 'SYP',
                'api_code' => 'USDSYP',
                'rates' => 514.97998,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            136 => 
            array (
                'id' => 137,
                'name' => 'Swazi Lilangeni',
                'alphacode' => 'SZL',
                'api_code' => 'USDSZL',
                'rates' => 13.138985,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            137 => 
            array (
                'id' => 138,
                'name' => 'Thai Baht',
                'alphacode' => 'THB',
                'api_code' => 'USDTHB',
                'rates' => 32.019946,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            138 => 
            array (
                'id' => 139,
                'name' => 'Tajikistani Somoni',
                'alphacode' => 'TJS',
                'api_code' => 'USDTJS',
                'rates' => 9.068503,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            139 => 
            array (
                'id' => 140,
                'name' => 'Turkmenistani Manat',
                'alphacode' => 'TMT',
                'api_code' => 'USDTMT',
                'rates' => 3.4,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            140 => 
            array (
                'id' => 141,
                'name' => 'Tunisian Dinar',
                'alphacode' => 'TND',
                'api_code' => 'USDTND',
                'rates' => 2.608797,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            141 => 
            array (
                'id' => 142,
                'name' => 'Tongan Paʻanga',
                'alphacode' => 'TOP',
                'api_code' => 'USDTOP',
                'rates' => 2.286903,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            142 => 
            array (
                'id' => 143,
                'name' => 'Turkish Lira',
                'alphacode' => 'TRY',
                'api_code' => 'USDTRY',
                'rates' => 4.514302,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            143 => 
            array (
                'id' => 144,
                'name' => 'Trinidad and Tobago Dollar',
                'alphacode' => 'TTD',
                'api_code' => 'USDTTD',
                'rates' => 6.649502,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            144 => 
            array (
                'id' => 145,
                'name' => 'New Taiwan Dollar',
                'alphacode' => 'TWD',
                'api_code' => 'USDTWD',
                'rates' => 29.818965,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            145 => 
            array (
                'id' => 146,
                'name' => 'Tanzanian Shilling',
                'alphacode' => 'TZS',
                'api_code' => 'USDTZS',
                'rates' => 2267.999621,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            146 => 
            array (
                'id' => 147,
                'name' => 'Ukrainian Hryvnia',
                'alphacode' => 'UAH',
                'api_code' => 'USDUAH',
                'rates' => 26.065001,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            147 => 
            array (
                'id' => 148,
                'name' => 'Ugandan Shilling',
                'alphacode' => 'UGX',
                'api_code' => 'USDUGX',
                'rates' => 3817.000236,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            148 => 
            array (
                'id' => 149,
                'name' => 'United States Dollar',
                'alphacode' => 'USD',
                'api_code' => 'USDUSD',
                'rates' => 1.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            149 => 
            array (
                'id' => 150,
                'name' => 'Uruguayan Peso',
                'alphacode' => 'UYU',
                'api_code' => 'USDUYU',
                'rates' => 31.259874,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:50',
            ),
            150 => 
            array (
                'id' => 151,
                'name' => 'Uzbekistan Som',
                'alphacode' => 'UZS',
                'api_code' => 'USDUZS',
                'rates' => 7914.000263,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            151 => 
            array (
                'id' => 152,
                'name' => 'Venezuelan Bolívar Fuerte',
                'alphacode' => 'VEF',
                'api_code' => 'USDVEF',
                'rates' => 79800.000092,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            152 => 
            array (
                'id' => 153,
                'name' => 'Vietnamese Dong',
                'alphacode' => 'VND',
                'api_code' => 'USDVND',
                'rates' => 22813.0,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            153 => 
            array (
                'id' => 154,
                'name' => 'Vanuatu Vatu',
                'alphacode' => 'VUV',
                'api_code' => 'USDVUV',
                'rates' => 106.660004,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            154 => 
            array (
                'id' => 155,
                'name' => 'Samoan Tala',
                'alphacode' => 'WST',
                'api_code' => 'USDWST',
                'rates' => 2.565598,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            155 => 
            array (
                'id' => 156,
                'name' => 'CFA Franc BEAC',
                'alphacode' => 'XAF',
                'api_code' => 'USDXAF',
                'rates' => 556.169983,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            156 => 
            array (
                'id' => 157,
            'name' => 'Silver (troy ounce)',
                'alphacode' => 'XAG',
                'api_code' => 'USDXAG',
                'rates' => 0.059128,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            157 => 
            array (
                'id' => 158,
            'name' => 'Gold (troy ounce)',
                'alphacode' => 'XAU',
                'api_code' => 'USDXAU',
                'rates' => 0.000769,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            158 => 
            array (
                'id' => 159,
                'name' => 'East Caribbean Dollar',
                'alphacode' => 'XCD',
                'api_code' => 'USDXCD',
                'rates' => 2.703078,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            159 => 
            array (
                'id' => 160,
                'name' => 'Special Drawing Rights',
                'alphacode' => 'XDR',
                'api_code' => 'USDXDR',
                'rates' => 0.704142,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            160 => 
            array (
                'id' => 161,
                'name' => 'CFA Franc BCEAO',
                'alphacode' => 'XOF',
                'api_code' => 'USDXOF',
                'rates' => 556.169983,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            161 => 
            array (
                'id' => 162,
                'name' => 'CFP Franc',
                'alphacode' => 'XPF',
                'api_code' => 'USDXPF',
                'rates' => 101.246215,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            162 => 
            array (
                'id' => 163,
                'name' => 'Yemeni Rial',
                'alphacode' => 'YER',
                'api_code' => 'USDYER',
                'rates' => 249.850006,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            163 => 
            array (
                'id' => 164,
                'name' => 'South African Rand',
                'alphacode' => 'ZAR',
                'api_code' => 'USDZAR',
                'rates' => 13.139799,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            164 => 
            array (
                'id' => 165,
            'name' => 'Zambian Kwacha (pre-2013)',
                'alphacode' => 'ZMK',
                'api_code' => 'USDZMK',
                'rates' => 9001.198917,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            165 => 
            array (
                'id' => 166,
                'name' => 'Zambian Kwacha',
                'alphacode' => 'ZMW',
                'api_code' => 'USDZMW',
                'rates' => 10.130075,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
            166 => 
            array (
                'id' => 167,
                'name' => 'Zimbabwean Dollar',
                'alphacode' => 'ZWL',
                'api_code' => 'USDZWL',
                'rates' => 322.355011,
                'created_at' => NULL,
                'updated_at' => '2018-06-11 18:26:51',
            ),
        ));
        
        
    }
}