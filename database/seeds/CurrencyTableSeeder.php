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
                    'rates' => NULL,
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Afghan Afghani',
                    'alphacode' => 'AFN',
                    'rates' => NULL,
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'Albanian Lek',
                    'alphacode' => 'ALL',
                    'rates' => NULL,
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'Armenian Dram',
                    'alphacode' => 'AMD',
                    'rates' => NULL,
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'Netherlands Antillean Guilder',
                    'alphacode' => 'ANG',
                    'rates' => NULL,
                ),
            5 =>
                array (
                    'id' => 6,
                    'name' => 'Angolan Kwanza',
                    'alphacode' => 'AOA',
                    'rates' => NULL,
                ),
            6 =>
                array (
                    'id' => 7,
                    'name' => 'Argentine Peso',
                    'alphacode' => 'ARS',
                    'rates' => NULL,
                ),
            7 =>
                array (
                    'id' => 8,
                    'name' => 'Australian Dollar',
                    'alphacode' => 'AUD',
                    'rates' => NULL,
                ),
            8 =>
                array (
                    'id' => 9,
                    'name' => 'Aruban Florin',
                    'alphacode' => 'AWG',
                    'rates' => NULL,
                ),
            9 =>
                array (
                    'id' => 10,
                    'name' => 'Azerbaijani Manat',
                    'alphacode' => 'AZN',
                    'rates' => NULL,
                ),
            10 =>
                array (
                    'id' => 11,
                    'name' => 'Bosnia-Herzegovina Convertible Mark',
                    'alphacode' => 'BAM',
                    'rates' => NULL,
                ),
            11 =>
                array (
                    'id' => 12,
                    'name' => 'Barbadian Dollar',
                    'alphacode' => 'BBD',
                    'rates' => NULL,
                ),
            12 =>
                array (
                    'id' => 13,
                    'name' => 'Bangladeshi Taka',
                    'alphacode' => 'BDT',
                    'rates' => NULL,
                ),
            13 =>
                array (
                    'id' => 14,
                    'name' => 'Bulgarian Lev',
                    'alphacode' => 'BGN',
                    'rates' => NULL,
                ),
            14 =>
                array (
                    'id' => 15,
                    'name' => 'Bahraini Dinar',
                    'alphacode' => 'BHD',
                    'rates' => NULL,
                ),
            15 =>
                array (
                    'id' => 16,
                    'name' => 'Burundian Franc',
                    'alphacode' => 'BIF',
                    'rates' => NULL,
                ),
            16 =>
                array (
                    'id' => 17,
                    'name' => 'Bermudan Dollar',
                    'alphacode' => 'BMD',
                    'rates' => NULL,
                ),
            17 =>
                array (
                    'id' => 18,
                    'name' => 'Brunei Dollar',
                    'alphacode' => 'BND',
                    'rates' => NULL,
                ),
            18 =>
                array (
                    'id' => 19,
                    'name' => 'Bolivian Boliviano',
                    'alphacode' => 'BOB',
                    'rates' => NULL,
                ),
            19 =>
                array (
                    'id' => 20,
                    'name' => 'Brazilian Real',
                    'alphacode' => 'BRL',
                    'rates' => NULL,
                ),
            20 =>
                array (
                    'id' => 21,
                    'name' => 'Bahamian Dollar',
                    'alphacode' => 'BSD',
                    'rates' => NULL,
                ),
            21 =>
                array (
                    'id' => 22,
                    'name' => 'Bitcoin',
                    'alphacode' => 'BTC',
                    'rates' => NULL,
                ),
            22 =>
                array (
                    'id' => 23,
                    'name' => 'Bhutanese Ngultrum',
                    'alphacode' => 'BTN',
                    'rates' => NULL,
                ),
            23 =>
                array (
                    'id' => 24,
                    'name' => 'Botswanan Pula',
                    'alphacode' => 'BWP',
                    'rates' => NULL,
                ),
            24 =>
                array (
                    'id' => 25,
                    'name' => 'Belarusian Ruble',
                    'alphacode' => 'BYR',
                    'rates' => NULL,
                ),
            25 =>
                array (
                    'id' => 26,
                    'name' => 'Belize Dollar',
                    'alphacode' => 'BZD',
                    'rates' => NULL,
                ),
            26 =>
                array (
                    'id' => 27,
                    'name' => 'Canadian Dollar',
                    'alphacode' => 'CAD',
                    'rates' => NULL,
                ),
            27 =>
                array (
                    'id' => 28,
                    'name' => 'Congolese Franc',
                    'alphacode' => 'CDF',
                    'rates' => NULL,
                ),
            28 =>
                array (
                    'id' => 29,
                    'name' => 'Swiss Franc',
                    'alphacode' => 'CHF',
                    'rates' => NULL,
                ),
            29 =>
                array (
                    'id' => 30,
                    'name' => 'Chilean Unit of Account (UF)',
                    'alphacode' => 'CLF',
                    'rates' => NULL,
                ),
            30 =>
                array (
                    'id' => 31,
                    'name' => 'Chilean Peso',
                    'alphacode' => 'CLP',
                    'rates' => NULL,
                ),
            31 =>
                array (
                    'id' => 32,
                    'name' => 'Chinese Yuan',
                    'alphacode' => 'CNY',
                    'rates' => NULL,
                ),
            32 =>
                array (
                    'id' => 33,
                    'name' => 'Colombian Peso',
                    'alphacode' => 'COP',
                    'rates' => NULL,
                ),
            33 =>
                array (
                    'id' => 34,
                    'name' => 'Costa Rican Colón',
                    'alphacode' => 'CRC',
                    'rates' => NULL,
                ),
            34 =>
                array (
                    'id' => 35,
                    'name' => 'Cuban Convertible Peso',
                    'alphacode' => 'CUC',
                    'rates' => NULL,
                ),
            35 =>
                array (
                    'id' => 36,
                    'name' => 'Cuban Peso',
                    'alphacode' => 'CUP',
                    'rates' => NULL,
                ),
            36 =>
                array (
                    'id' => 37,
                    'name' => 'Cape Verdean Escudo',
                    'alphacode' => 'CVE',
                    'rates' => NULL,
                ),
            37 =>
                array (
                    'id' => 38,
                    'name' => 'Czech Republic Koruna',
                    'alphacode' => 'CZK',
                    'rates' => NULL,
                ),
            38 =>
                array (
                    'id' => 39,
                    'name' => 'Djiboutian Franc',
                    'alphacode' => 'DJF',
                    'rates' => NULL,
                ),
            39 =>
                array (
                    'id' => 40,
                    'name' => 'Danish Krone',
                    'alphacode' => 'DKK',
                    'rates' => NULL,
                ),
            40 =>
                array (
                    'id' => 41,
                    'name' => 'Dominican Peso',
                    'alphacode' => 'DOP',
                    'rates' => NULL,
                ),
            41 =>
                array (
                    'id' => 42,
                    'name' => 'Algerian Dinar',
                    'alphacode' => 'DZD',
                    'rates' => NULL,
                ),
            42 =>
                array (
                    'id' => 43,
                    'name' => 'Egyptian Pound',
                    'alphacode' => 'EGP',
                    'rates' => NULL,
                ),
            43 =>
                array (
                    'id' => 44,
                    'name' => 'Eritrean Nakfa',
                    'alphacode' => 'ERN',
                    'rates' => NULL,
                ),
            44 =>
                array (
                    'id' => 45,
                    'name' => 'Ethiopian Birr',
                    'alphacode' => 'ETB',
                    'rates' => NULL,
                ),
            45 =>
                array (
                    'id' => 46,
                    'name' => 'Euro',
                    'alphacode' => 'EUR',
                    'rates' => NULL,
                ),
            46 =>
                array (
                    'id' => 47,
                    'name' => 'Fijian Dollar',
                    'alphacode' => 'FJD',
                    'rates' => NULL,
                ),
            47 =>
                array (
                    'id' => 48,
                    'name' => 'Falkland Islands Pound',
                    'alphacode' => 'FKP',
                    'rates' => NULL,
                ),
            48 =>
                array (
                    'id' => 49,
                    'name' => 'British Pound Sterling',
                    'alphacode' => 'GBP',
                    'rates' => NULL,
                ),
            49 =>
                array (
                    'id' => 50,
                    'name' => 'Georgian Lari',
                    'alphacode' => 'GEL',
                    'rates' => NULL,
                ),
            50 =>
                array (
                    'id' => 51,
                    'name' => 'Guernsey Pound',
                    'alphacode' => 'GGP',
                    'rates' => NULL,
                ),
            51 =>
                array (
                    'id' => 52,
                    'name' => 'Ghanaian Cedi',
                    'alphacode' => 'GHS',
                    'rates' => NULL,
                ),
            52 =>
                array (
                    'id' => 53,
                    'name' => 'Gibraltar Pound',
                    'alphacode' => 'GIP',
                    'rates' => NULL,
                ),
            53 =>
                array (
                    'id' => 54,
                    'name' => 'Gambian Dalasi',
                    'alphacode' => 'GMD',
                    'rates' => NULL,
                ),
            54 =>
                array (
                    'id' => 55,
                    'name' => 'Guinean Franc',
                    'alphacode' => 'GNF',
                    'rates' => NULL,
                ),
            55 =>
                array (
                    'id' => 56,
                    'name' => 'Guatemalan Quetzal',
                    'alphacode' => 'GTQ',
                    'rates' => NULL,
                ),
            56 =>
                array (
                    'id' => 57,
                    'name' => 'Guyanaese Dollar',
                    'alphacode' => 'GYD',
                    'rates' => NULL,
                ),
            57 =>
                array (
                    'id' => 58,
                    'name' => 'Hong Kong Dollar',
                    'alphacode' => 'HKD',
                    'rates' => NULL,
                ),
            58 =>
                array (
                    'id' => 59,
                    'name' => 'Honduran Lempira',
                    'alphacode' => 'HNL',
                    'rates' => NULL,
                ),
            59 =>
                array (
                    'id' => 60,
                    'name' => 'Croatian Kuna',
                    'alphacode' => 'HRK',
                    'rates' => NULL,
                ),
            60 =>
                array (
                    'id' => 61,
                    'name' => 'Haitian Gourde',
                    'alphacode' => 'HTG',
                    'rates' => NULL,
                ),
            61 =>
                array (
                    'id' => 62,
                    'name' => 'Hungarian Forint',
                    'alphacode' => 'HUF',
                    'rates' => NULL,
                ),
            62 =>
                array (
                    'id' => 63,
                    'name' => 'Indonesian Rupiah',
                    'alphacode' => 'IDR',
                    'rates' => NULL,
                ),
            63 =>
                array (
                    'id' => 64,
                    'name' => 'Israeli New Sheqel',
                    'alphacode' => 'ILS',
                    'rates' => NULL,
                ),
            64 =>
                array (
                    'id' => 65,
                    'name' => 'Manx pound',
                    'alphacode' => 'IMP',
                    'rates' => NULL,
                ),
            65 =>
                array (
                    'id' => 66,
                    'name' => 'Indian Rupee',
                    'alphacode' => 'INR',
                    'rates' => NULL,
                ),
            66 =>
                array (
                    'id' => 67,
                    'name' => 'Iraqi Dinar',
                    'alphacode' => 'IQD',
                    'rates' => NULL,
                ),
            67 =>
                array (
                    'id' => 68,
                    'name' => 'Iranian Rial',
                    'alphacode' => 'IRR',
                    'rates' => NULL,
                ),
            68 =>
                array (
                    'id' => 69,
                    'name' => 'Icelandic Króna',
                    'alphacode' => 'ISK',
                    'rates' => NULL,
                ),
            69 =>
                array (
                    'id' => 70,
                    'name' => 'Jersey Pound',
                    'alphacode' => 'JEP',
                    'rates' => NULL,
                ),
            70 =>
                array (
                    'id' => 71,
                    'name' => 'Jamaican Dollar',
                    'alphacode' => 'JMD',
                    'rates' => NULL,
                ),
            71 =>
                array (
                    'id' => 72,
                    'name' => 'Jordanian Dinar',
                    'alphacode' => 'JOD',
                    'rates' => NULL,
                ),
            72 =>
                array (
                    'id' => 73,
                    'name' => 'Japanese Yen',
                    'alphacode' => 'JPY',
                    'rates' => NULL,
                ),
            73 =>
                array (
                    'id' => 74,
                    'name' => 'Kenyan Shilling',
                    'alphacode' => 'KES',
                    'rates' => NULL,
                ),
            74 =>
                array (
                    'id' => 75,
                    'name' => 'Kyrgystani Som',
                    'alphacode' => 'KGS',
                    'rates' => NULL,
                ),
            75 =>
                array (
                    'id' => 76,
                    'name' => 'Cambodian Riel',
                    'alphacode' => 'KHR',
                    'rates' => NULL,
                ),
            76 =>
                array (
                    'id' => 77,
                    'name' => 'Comorian Franc',
                    'alphacode' => 'KMF',
                    'rates' => NULL,
                ),
            77 =>
                array (
                    'id' => 78,
                    'name' => 'North Korean Won',
                    'alphacode' => 'KPW',
                    'rates' => NULL,
                ),
            78 =>
                array (
                    'id' => 79,
                    'name' => 'South Korean Won',
                    'alphacode' => 'KRW',
                    'rates' => NULL,
                ),
            79 =>
                array (
                    'id' => 80,
                    'name' => 'Kuwaiti Dinar',
                    'alphacode' => 'KWD',
                    'rates' => NULL,
                ),
            80 =>
                array (
                    'id' => 81,
                    'name' => 'Cayman Islands Dollar',
                    'alphacode' => 'KYD',
                    'rates' => NULL,
                ),
            81 =>
                array (
                    'id' => 82,
                    'name' => 'Kazakhstani Tenge',
                    'alphacode' => 'KZT',
                    'rates' => NULL,
                ),
            82 =>
                array (
                    'id' => 83,
                    'name' => 'Laotian Kip',
                    'alphacode' => 'LAK',
                    'rates' => NULL,
                ),
            83 =>
                array (
                    'id' => 84,
                    'name' => 'Lebanese Pound',
                    'alphacode' => 'LBP',
                    'rates' => NULL,
                ),
            84 =>
                array (
                    'id' => 85,
                    'name' => 'Sri Lankan Rupee',
                    'alphacode' => 'LKR',
                    'rates' => NULL,
                ),
            85 =>
                array (
                    'id' => 86,
                    'name' => 'Liberian Dollar',
                    'alphacode' => 'LRD',
                    'rates' => NULL,
                ),
            86 =>
                array (
                    'id' => 87,
                    'name' => 'Lesotho Loti',
                    'alphacode' => 'LSL',
                    'rates' => NULL,
                ),
            87 =>
                array (
                    'id' => 88,
                    'name' => 'Lithuanian Litas',
                    'alphacode' => 'LTL',
                    'rates' => NULL,
                ),
            88 =>
                array (
                    'id' => 89,
                    'name' => 'Latvian Lats',
                    'alphacode' => 'LVL',
                    'rates' => NULL,
                ),
            89 =>
                array (
                    'id' => 90,
                    'name' => 'Libyan Dinar',
                    'alphacode' => 'LYD',
                    'rates' => NULL,
                ),
            90 =>
                array (
                    'id' => 91,
                    'name' => 'Moroccan Dirham',
                    'alphacode' => 'MAD',
                    'rates' => NULL,
                ),
            91 =>
                array (
                    'id' => 92,
                    'name' => 'Moldovan Leu',
                    'alphacode' => 'MDL',
                    'rates' => NULL,
                ),
            92 =>
                array (
                    'id' => 93,
                    'name' => 'Malagasy Ariary',
                    'alphacode' => 'MGA',
                    'rates' => NULL,
                ),
            93 =>
                array (
                    'id' => 94,
                    'name' => 'Macedonian Denar',
                    'alphacode' => 'MKD',
                    'rates' => NULL,
                ),
            94 =>
                array (
                    'id' => 95,
                    'name' => 'Myanma Kyat',
                    'alphacode' => 'MMK',
                    'rates' => NULL,
                ),
            95 =>
                array (
                    'id' => 96,
                    'name' => 'Mongolian Tugrik',
                    'alphacode' => 'MNT',
                    'rates' => NULL,
                ),
            96 =>
                array (
                    'id' => 97,
                    'name' => 'Macanese Pataca',
                    'alphacode' => 'MOP',
                    'rates' => NULL,
                ),
            97 =>
                array (
                    'id' => 98,
                    'name' => 'Mauritanian Ouguiya',
                    'alphacode' => 'MRO',
                    'rates' => NULL,
                ),
            98 =>
                array (
                    'id' => 99,
                    'name' => 'Mauritian Rupee',
                    'alphacode' => 'MUR',
                    'rates' => NULL,
                ),
            99 =>
                array (
                    'id' => 100,
                    'name' => 'Maldivian Rufiyaa',
                    'alphacode' => 'MVR',
                    'rates' => NULL,
                ),
            100 =>
                array (
                    'id' => 101,
                    'name' => 'Malawian Kwacha',
                    'alphacode' => 'MWK',
                    'rates' => NULL,
                ),
            101 =>
                array (
                    'id' => 102,
                    'name' => 'Mexican Peso',
                    'alphacode' => 'MXN',
                    'rates' => NULL,
                ),
            102 =>
                array (
                    'id' => 103,
                    'name' => 'Malaysian Ringgit',
                    'alphacode' => 'MYR',
                    'rates' => NULL,
                ),
            103 =>
                array (
                    'id' => 104,
                    'name' => 'Mozambican Metical',
                    'alphacode' => 'MZN',
                    'rates' => NULL,
                ),
            104 =>
                array (
                    'id' => 105,
                    'name' => 'Namibian Dollar',
                    'alphacode' => 'NAD',
                    'rates' => NULL,
                ),
            105 =>
                array (
                    'id' => 106,
                    'name' => 'Nigerian Naira',
                    'alphacode' => 'NGN',
                    'rates' => NULL,
                ),
            106 =>
                array (
                    'id' => 107,
                    'name' => 'Nicaraguan Córdoba',
                    'alphacode' => 'NIO',
                    'rates' => NULL,
                ),
            107 =>
                array (
                    'id' => 108,
                    'name' => 'Norwegian Krone',
                    'alphacode' => 'NOK',
                    'rates' => NULL,
                ),
            108 =>
                array (
                    'id' => 109,
                    'name' => 'Nepalese Rupee',
                    'alphacode' => 'NPR',
                    'rates' => NULL,
                ),
            109 =>
                array (
                    'id' => 110,
                    'name' => 'New Zealand Dollar',
                    'alphacode' => 'NZD',
                    'rates' => NULL,
                ),
            110 =>
                array (
                    'id' => 111,
                    'name' => 'Omani Rial',
                    'alphacode' => 'OMR',
                    'rates' => NULL,
                ),
            111 =>
                array (
                    'id' => 112,
                    'name' => 'Panamanian Balboa',
                    'alphacode' => 'PAB',
                    'rates' => NULL,
                ),
            112 =>
                array (
                    'id' => 113,
                    'name' => 'Peruvian Nuevo Sol',
                    'alphacode' => 'PEN',
                    'rates' => NULL,
                ),
            113 =>
                array (
                    'id' => 114,
                    'name' => 'Papua New Guinean Kina',
                    'alphacode' => 'PGK',
                    'rates' => NULL,
                ),
            114 =>
                array (
                    'id' => 115,
                    'name' => 'Philippine Peso',
                    'alphacode' => 'PHP',
                    'rates' => NULL,
                ),
            115 =>
                array (
                    'id' => 116,
                    'name' => 'Pakistani Rupee',
                    'alphacode' => 'PKR',
                    'rates' => NULL,
                ),
            116 =>
                array (
                    'id' => 117,
                    'name' => 'Polish Zloty',
                    'alphacode' => 'PLN',
                    'rates' => NULL,
                ),
            117 =>
                array (
                    'id' => 118,
                    'name' => 'Paraguayan Guarani',
                    'alphacode' => 'PYG',
                    'rates' => NULL,
                ),
            118 =>
                array (
                    'id' => 119,
                    'name' => 'Qatari Rial',
                    'alphacode' => 'QAR',
                    'rates' => NULL,
                ),
            119 =>
                array (
                    'id' => 120,
                    'name' => 'Romanian Leu',
                    'alphacode' => 'RON',
                    'rates' => NULL,
                ),
            120 =>
                array (
                    'id' => 121,
                    'name' => 'Serbian Dinar',
                    'alphacode' => 'RSD',
                    'rates' => NULL,
                ),
            121 =>
                array (
                    'id' => 122,
                    'name' => 'Russian Ruble',
                    'alphacode' => 'RUB',
                    'rates' => NULL,
                ),
            122 =>
                array (
                    'id' => 123,
                    'name' => 'Rwandan Franc',
                    'alphacode' => 'RWF',
                    'rates' => NULL,
                ),
            123 =>
                array (
                    'id' => 124,
                    'name' => 'Saudi Riyal',
                    'alphacode' => 'SAR',
                    'rates' => NULL,
                ),
            124 =>
                array (
                    'id' => 125,
                    'name' => 'Solomon Islands Dollar',
                    'alphacode' => 'SBD',
                    'rates' => NULL,
                ),
            125 =>
                array (
                    'id' => 126,
                    'name' => 'Seychellois Rupee',
                    'alphacode' => 'SCR',
                    'rates' => NULL,
                ),
            126 =>
                array (
                    'id' => 127,
                    'name' => 'Sudanese Pound',
                    'alphacode' => 'SDG',
                    'rates' => NULL,
                ),
            127 =>
                array (
                    'id' => 128,
                    'name' => 'Swedish Krona',
                    'alphacode' => 'SEK',
                    'rates' => NULL,
                ),
            128 =>
                array (
                    'id' => 129,
                    'name' => 'Singapore Dollar',
                    'alphacode' => 'SGD',
                    'rates' => NULL,
                ),
            129 =>
                array (
                    'id' => 130,
                    'name' => 'Saint Helena Pound',
                    'alphacode' => 'SHP',
                    'rates' => NULL,
                ),
            130 =>
                array (
                    'id' => 131,
                    'name' => 'Sierra Leonean Leone',
                    'alphacode' => 'SLL',
                    'rates' => NULL,
                ),
            131 =>
                array (
                    'id' => 132,
                    'name' => 'Somali Shilling',
                    'alphacode' => 'SOS',
                    'rates' => NULL,
                ),
            132 =>
                array (
                    'id' => 133,
                    'name' => 'Surinamese Dollar',
                    'alphacode' => 'SRD',
                    'rates' => NULL,
                ),
            133 =>
                array (
                    'id' => 134,
                    'name' => 'São Tomé and Príncipe Dobra',
                    'alphacode' => 'STD',
                    'rates' => NULL,
                ),
            134 =>
                array (
                    'id' => 135,
                    'name' => 'Salvadoran Colón',
                    'alphacode' => 'SVC',
                    'rates' => NULL,
                ),
            135 =>
                array (
                    'id' => 136,
                    'name' => 'Syrian Pound',
                    'alphacode' => 'SYP',
                    'rates' => NULL,
                ),
            136 =>
                array (
                    'id' => 137,
                    'name' => 'Swazi Lilangeni',
                    'alphacode' => 'SZL',
                    'rates' => NULL,
                ),
            137 =>
                array (
                    'id' => 138,
                    'name' => 'Thai Baht',
                    'alphacode' => 'THB',
                    'rates' => NULL,
                ),
            138 =>
                array (
                    'id' => 139,
                    'name' => 'Tajikistani Somoni',
                    'alphacode' => 'TJS',
                    'rates' => NULL,
                ),
            139 =>
                array (
                    'id' => 140,
                    'name' => 'Turkmenistani Manat',
                    'alphacode' => 'TMT',
                    'rates' => NULL,
                ),
            140 =>
                array (
                    'id' => 141,
                    'name' => 'Tunisian Dinar',
                    'alphacode' => 'TND',
                    'rates' => NULL,
                ),
            141 =>
                array (
                    'id' => 142,
                    'name' => 'Tongan Paʻanga',
                    'alphacode' => 'TOP',
                    'rates' => NULL,
                ),
            142 =>
                array (
                    'id' => 143,
                    'name' => 'Turkish Lira',
                    'alphacode' => 'TRY',
                    'rates' => NULL,
                ),
            143 =>
                array (
                    'id' => 144,
                    'name' => 'Trinidad and Tobago Dollar',
                    'alphacode' => 'TTD',
                    'rates' => NULL,
                ),
            144 =>
                array (
                    'id' => 145,
                    'name' => 'New Taiwan Dollar',
                    'alphacode' => 'TWD',
                    'rates' => NULL,
                ),
            145 =>
                array (
                    'id' => 146,
                    'name' => 'Tanzanian Shilling',
                    'alphacode' => 'TZS',
                    'rates' => NULL,
                ),
            146 =>
                array (
                    'id' => 147,
                    'name' => 'Ukrainian Hryvnia',
                    'alphacode' => 'UAH',
                    'rates' => NULL,
                ),
            147 =>
                array (
                    'id' => 148,
                    'name' => 'Ugandan Shilling',
                    'alphacode' => 'UGX',
                    'rates' => NULL,
                ),
            148 =>
                array (
                    'id' => 149,
                    'name' => 'United States Dollar',
                    'alphacode' => 'USD',
                    'rates' => NULL,
                ),
            149 =>
                array (
                    'id' => 150,
                    'name' => 'Uruguayan Peso',
                    'alphacode' => 'UYU',
                    'rates' => NULL,
                ),
            150 =>
                array (
                    'id' => 151,
                    'name' => 'Uzbekistan Som',
                    'alphacode' => 'UZS',
                    'rates' => NULL,
                ),
            151 =>
                array (
                    'id' => 152,
                    'name' => 'Venezuelan Bolívar Fuerte',
                    'alphacode' => 'VEF',
                    'rates' => NULL,
                ),
            152 =>
                array (
                    'id' => 153,
                    'name' => 'Vietnamese Dong',
                    'alphacode' => 'VND',
                    'rates' => NULL,
                ),
            153 =>
                array (
                    'id' => 154,
                    'name' => 'Vanuatu Vatu',
                    'alphacode' => 'VUV',
                    'rates' => NULL,
                ),
            154 =>
                array (
                    'id' => 155,
                    'name' => 'Samoan Tala',
                    'alphacode' => 'WST',
                    'rates' => NULL,
                ),
            155 =>
                array (
                    'id' => 156,
                    'name' => 'CFA Franc BEAC',
                    'alphacode' => 'XAF',
                    'rates' => NULL,
                ),
            156 =>
                array (
                    'id' => 157,
                    'name' => 'Silver (troy ounce)',
                    'alphacode' => 'XAG',
                    'rates' => NULL,
                ),
            157 =>
                array (
                    'id' => 158,
                    'name' => 'Gold (troy ounce)',
                    'alphacode' => 'XAU',
                    'rates' => NULL,
                ),
            158 =>
                array (
                    'id' => 159,
                    'name' => 'East Caribbean Dollar',
                    'alphacode' => 'XCD',
                    'rates' => NULL,
                ),
            159 =>
                array (
                    'id' => 160,
                    'name' => 'Special Drawing Rights',
                    'alphacode' => 'XDR',
                    'rates' => NULL,
                ),
            160 =>
                array (
                    'id' => 161,
                    'name' => 'CFA Franc BCEAO',
                    'alphacode' => 'XOF',
                    'rates' => NULL,
                ),
            161 =>
                array (
                    'id' => 162,
                    'name' => 'CFP Franc',
                    'alphacode' => 'XPF',
                    'rates' => NULL,
                ),
            162 =>
                array (
                    'id' => 163,
                    'name' => 'Yemeni Rial',
                    'alphacode' => 'YER',
                    'rates' => NULL,
                ),
            163 =>
                array (
                    'id' => 164,
                    'name' => 'South African Rand',
                    'alphacode' => 'ZAR',
                    'rates' => NULL,
                ),
            164 =>
                array (
                    'id' => 165,
                    'name' => 'Zambian Kwacha (pre-2013)',
                    'alphacode' => 'ZMK',
                    'rates' => NULL,
                ),
            165 =>
                array (
                    'id' => 166,
                    'name' => 'Zambian Kwacha',
                    'alphacode' => 'ZMW',
                    'rates' => NULL,
                ),
            166 =>
                array (
                    'id' => 167,
                    'name' => 'Zimbabwean Dollar',
                    'alphacode' => 'ZWL',
                    'rates' => NULL,
                ),
        ));


    }
}