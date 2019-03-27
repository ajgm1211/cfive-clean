<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('countries')->delete();
        
        \DB::table('countries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'AD',
                'name' => 'Andorra',
                'continent' => 'EU',
                'variation' => '{"type": ["andorra"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'AE',
                'name' => 'United Arab Emirates',
                'continent' => 'AS',
                'variation' => '{"type": ["united arab emirates"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'AF',
                'name' => 'Afghanistan',
                'continent' => 'AS',
                'variation' => '{"type": ["afghanistan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'AG',
                'name' => 'Antigua and Barbuda',
                'continent' => 'NA',
                'variation' => '{"type": ["antigua and barbuda"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'AI',
                'name' => 'Anguilla',
                'continent' => 'NA',
                'variation' => '{"type": ["anguilla"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'AL',
                'name' => 'Albania',
                'continent' => 'EU',
                'variation' => '{"type": ["albania"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'AM',
                'name' => 'Armenia',
                'continent' => 'AS',
                'variation' => '{"type": ["armenia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'AO',
                'name' => 'Angola',
                'continent' => 'AF',
                'variation' => '{"type": ["angola"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'AQ',
                'name' => 'Antarctica',
                'continent' => 'AN',
                'variation' => '{"type": ["antarctica"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'AR',
                'name' => 'Argentina',
                'continent' => 'SA',
                'variation' => '{"type": ["argentina"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'AS',
                'name' => 'American Samoa',
                'continent' => 'OC',
                'variation' => '{"type": ["american samoa"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'AT',
                'name' => 'Austria',
                'continent' => 'EU',
                'variation' => '{"type": ["austria"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'AU',
                'name' => 'Australia',
                'continent' => 'OC',
                'variation' => '{"type": ["australia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'AW',
                'name' => 'Aruba',
                'continent' => 'NA',
                'variation' => '{"type": ["aruba"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'AZ',
                'name' => 'Azerbaijan',
                'continent' => 'AS',
                'variation' => '{"type": ["azerbaijan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'BA',
                'name' => 'Bosnia and Herzegovina',
                'continent' => 'EU',
                'variation' => '{"type": ["bosnia and herzegovina"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'BB',
                'name' => 'Barbados',
                'continent' => 'NA',
                'variation' => '{"type": ["barbados"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'BD',
                'name' => 'Bangladesh',
                'continent' => 'AS',
                'variation' => '{"type": ["bangladesh"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'BE',
                'name' => 'Belgium',
                'continent' => 'EU',
                'variation' => '{"type": ["belgium"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'BF',
                'name' => 'Burkina Faso',
                'continent' => 'AF',
                'variation' => '{"type": ["burkina faso"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'BG',
                'name' => 'Bulgaria',
                'continent' => 'EU',
                'variation' => '{"type": ["bulgaria"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'BH',
                'name' => 'Bahrain',
                'continent' => 'AS',
                'variation' => '{"type": ["bahrain"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'BI',
                'name' => 'Burundi',
                'continent' => 'AF',
                'variation' => '{"type": ["burundi"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:01',
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'BJ',
                'name' => 'Benin',
                'continent' => 'AF',
                'variation' => '{"type": ["benin"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'BL',
                'name' => 'Saint Barthélemy',
                'continent' => 'NA',
                'variation' => '{"type": ["saint barthélemy"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'BM',
                'name' => 'Bermuda',
                'continent' => 'NA',
                'variation' => '{"type": ["bermuda"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'BN',
                'name' => 'Brunei',
                'continent' => 'AS',
                'variation' => '{"type": ["brunei"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'BO',
                'name' => 'Bolivia',
                'continent' => 'SA',
                'variation' => '{"type": ["bolivia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'BQ',
                'name' => 'Caribbean Netherlands',
                'continent' => 'NA',
                'variation' => '{"type": ["caribbean netherlands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'BR',
                'name' => 'Brazil',
                'continent' => 'SA',
                'variation' => '{"type": ["brazil"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'BS',
                'name' => 'Bahamas',
                'continent' => 'NA',
                'variation' => '{"type": ["bahamas"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'BT',
                'name' => 'Bhutan',
                'continent' => 'AS',
                'variation' => '{"type": ["bhutan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'BW',
                'name' => 'Botswana',
                'continent' => 'AF',
                'variation' => '{"type": ["botswana"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'BY',
                'name' => 'Belarus',
                'continent' => 'EU',
                'variation' => '{"type": ["belarus"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'BZ',
                'name' => 'Belize',
                'continent' => 'NA',
                'variation' => '{"type": ["belize"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'CA',
                'name' => 'Canada',
                'continent' => 'NA',
                'variation' => '{"type": ["canada"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'CC',
            'name' => 'Cocos (Keeling) Islands',
                'continent' => 'AS',
            'variation' => '{"type": ["cocos (keeling) islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'CD',
            'name' => 'Congo (Kinshasa)',
                'continent' => 'AF',
            'variation' => '{"type": ["congo (kinshasa)"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            38 => 
            array (
                'id' => 39,
                'code' => 'CF',
                'name' => 'Central African Republic',
                'continent' => 'AF',
                'variation' => '{"type": ["central african republic"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            39 => 
            array (
                'id' => 40,
                'code' => 'CG',
            'name' => 'Congo (Brazzaville)',
                'continent' => 'AF',
            'variation' => '{"type": ["congo (brazzaville)"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            40 => 
            array (
                'id' => 41,
                'code' => 'CH',
                'name' => 'Switzerland',
                'continent' => 'EU',
                'variation' => '{"type": ["switzerland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            41 => 
            array (
                'id' => 42,
                'code' => 'CI',
                'name' => 'Côte d\'Ivoire',
                'continent' => 'AF',
                'variation' => '{"type": ["côte d\'ivoire"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            42 => 
            array (
                'id' => 43,
                'code' => 'CK',
                'name' => 'Cook Islands',
                'continent' => 'OC',
                'variation' => '{"type": ["cook islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            43 => 
            array (
                'id' => 44,
                'code' => 'CL',
                'name' => 'Chile',
                'continent' => 'SA',
                'variation' => '{"type": ["chile"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            44 => 
            array (
                'id' => 45,
                'code' => 'CM',
                'name' => 'Cameroon',
                'continent' => 'AF',
                'variation' => '{"type": ["cameroon"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:02',
            ),
            45 => 
            array (
                'id' => 46,
                'code' => 'CN',
                'name' => 'China',
                'continent' => 'AS',
                'variation' => '{"type": ["china"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            46 => 
            array (
                'id' => 47,
                'code' => 'CO',
                'name' => 'Colombia',
                'continent' => 'SA',
                'variation' => '{"type": ["colombia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            47 => 
            array (
                'id' => 48,
                'code' => 'CR',
                'name' => 'Costa Rica',
                'continent' => 'NA',
                'variation' => '{"type": ["costa rica"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            48 => 
            array (
                'id' => 49,
                'code' => 'CU',
                'name' => 'Cuba',
                'continent' => 'NA',
                'variation' => '{"type": ["cuba"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            49 => 
            array (
                'id' => 50,
                'code' => 'CV',
                'name' => 'Cape Verde',
                'continent' => 'AF',
                'variation' => '{"type": ["cape verde"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            50 => 
            array (
                'id' => 51,
                'code' => 'CW',
                'name' => 'Curaçao',
                'continent' => 'NA',
                'variation' => '{"type": ["curaçao"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            51 => 
            array (
                'id' => 52,
                'code' => 'CX',
                'name' => 'Christmas Island',
                'continent' => 'AS',
                'variation' => '{"type": ["christmas island"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            52 => 
            array (
                'id' => 53,
                'code' => 'CY',
                'name' => 'Cyprus',
                'continent' => 'AS',
                'variation' => '{"type": ["cyprus"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            53 => 
            array (
                'id' => 54,
                'code' => 'CZ',
                'name' => 'Czechia',
                'continent' => 'EU',
                'variation' => '{"type": ["czechia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            54 => 
            array (
                'id' => 55,
                'code' => 'DE',
                'name' => 'Germany',
                'continent' => 'EU',
                'variation' => '{"type": ["germany"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            55 => 
            array (
                'id' => 56,
                'code' => 'DJ',
                'name' => 'Djibouti',
                'continent' => 'AF',
                'variation' => '{"type": ["djibouti"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            56 => 
            array (
                'id' => 57,
                'code' => 'DK',
                'name' => 'Denmark',
                'continent' => 'EU',
                'variation' => '{"type": ["denmark"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            57 => 
            array (
                'id' => 58,
                'code' => 'DM',
                'name' => 'Dominica',
                'continent' => 'NA',
                'variation' => '{"type": ["dominica"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            58 => 
            array (
                'id' => 59,
                'code' => 'DO',
                'name' => 'Dominican Republic',
                'continent' => 'NA',
                'variation' => '{"type": ["dominican republic"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            59 => 
            array (
                'id' => 60,
                'code' => 'DZ',
                'name' => 'Algeria',
                'continent' => 'AF',
                'variation' => '{"type": ["algeria"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            60 => 
            array (
                'id' => 61,
                'code' => 'EC',
                'name' => 'Ecuador',
                'continent' => 'SA',
                'variation' => '{"type": ["ecuador"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            61 => 
            array (
                'id' => 62,
                'code' => 'EE',
                'name' => 'Estonia',
                'continent' => 'EU',
                'variation' => '{"type": ["estonia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            62 => 
            array (
                'id' => 63,
                'code' => 'EG',
                'name' => 'Egypt',
                'continent' => 'AF',
                'variation' => '{"type": ["egypt"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            63 => 
            array (
                'id' => 64,
                'code' => 'EH',
                'name' => 'Western Sahara',
                'continent' => 'AF',
                'variation' => '{"type": ["western sahara"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            64 => 
            array (
                'id' => 65,
                'code' => 'ER',
                'name' => 'Eritrea',
                'continent' => 'AF',
                'variation' => '{"type": ["eritrea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            65 => 
            array (
                'id' => 66,
                'code' => 'ES',
                'name' => 'Spain',
                'continent' => 'EU',
                'variation' => '{"type": ["spain"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            66 => 
            array (
                'id' => 67,
                'code' => 'ET',
                'name' => 'Ethiopia',
                'continent' => 'AF',
                'variation' => '{"type": ["ethiopia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            67 => 
            array (
                'id' => 68,
                'code' => 'FI',
                'name' => 'Finland',
                'continent' => 'EU',
                'variation' => '{"type": ["finland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            68 => 
            array (
                'id' => 69,
                'code' => 'FJ',
                'name' => 'Fiji',
                'continent' => 'OC',
                'variation' => '{"type": ["fiji"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            69 => 
            array (
                'id' => 70,
                'code' => 'FK',
                'name' => 'Falkland Islands',
                'continent' => 'SA',
                'variation' => '{"type": ["falkland islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            70 => 
            array (
                'id' => 71,
                'code' => 'FM',
                'name' => 'Micronesia',
                'continent' => 'OC',
                'variation' => '{"type": ["micronesia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            71 => 
            array (
                'id' => 72,
                'code' => 'FO',
                'name' => 'Faroe Islands',
                'continent' => 'EU',
                'variation' => '{"type": ["faroe islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:03',
            ),
            72 => 
            array (
                'id' => 73,
                'code' => 'FR',
                'name' => 'France',
                'continent' => 'EU',
                'variation' => '{"type": ["france"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            73 => 
            array (
                'id' => 74,
                'code' => 'GA',
                'name' => 'Gabon',
                'continent' => 'AF',
                'variation' => '{"type": ["gabon"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            74 => 
            array (
                'id' => 75,
                'code' => 'GB',
                'name' => 'United Kingdom',
                'continent' => 'EU',
                'variation' => '{"type": ["united kingdom"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            75 => 
            array (
                'id' => 76,
                'code' => 'GD',
                'name' => 'Grenada',
                'continent' => 'NA',
                'variation' => '{"type": ["grenada"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            76 => 
            array (
                'id' => 77,
                'code' => 'GE',
                'name' => 'Georgia',
                'continent' => 'AS',
                'variation' => '{"type": ["georgia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            77 => 
            array (
                'id' => 78,
                'code' => 'GF',
                'name' => 'French Guiana',
                'continent' => 'SA',
                'variation' => '{"type": ["french guiana"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            78 => 
            array (
                'id' => 79,
                'code' => 'GG',
                'name' => 'Guernsey',
                'continent' => 'EU',
                'variation' => '{"type": ["guernsey"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            79 => 
            array (
                'id' => 80,
                'code' => 'GH',
                'name' => 'Ghana',
                'continent' => 'AF',
                'variation' => '{"type": ["ghana"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            80 => 
            array (
                'id' => 81,
                'code' => 'GI',
                'name' => 'Gibraltar',
                'continent' => 'EU',
                'variation' => '{"type": ["gibraltar"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            81 => 
            array (
                'id' => 82,
                'code' => 'GL',
                'name' => 'Greenland',
                'continent' => 'NA',
                'variation' => '{"type": ["greenland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            82 => 
            array (
                'id' => 83,
                'code' => 'GM',
                'name' => 'Gambia',
                'continent' => 'AF',
                'variation' => '{"type": ["gambia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            83 => 
            array (
                'id' => 84,
                'code' => 'GN',
                'name' => 'Guinea',
                'continent' => 'AF',
                'variation' => '{"type": ["guinea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            84 => 
            array (
                'id' => 85,
                'code' => 'GP',
                'name' => 'Guadeloupe',
                'continent' => 'NA',
                'variation' => '{"type": ["guadeloupe"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            85 => 
            array (
                'id' => 86,
                'code' => 'GQ',
                'name' => 'Equatorial Guinea',
                'continent' => 'AF',
                'variation' => '{"type": ["equatorial guinea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            86 => 
            array (
                'id' => 87,
                'code' => 'GR',
                'name' => 'Greece',
                'continent' => 'EU',
                'variation' => '{"type": ["greece"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            87 => 
            array (
                'id' => 88,
                'code' => 'GS',
                'name' => 'South Georgia and the South Sandwich Islands',
                'continent' => 'AN',
                'variation' => '{"type": ["south georgia and the south sandwich islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            88 => 
            array (
                'id' => 89,
                'code' => 'GT',
                'name' => 'Guatemala',
                'continent' => 'NA',
                'variation' => '{"type": ["guatemala"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            89 => 
            array (
                'id' => 90,
                'code' => 'GU',
                'name' => 'Guam',
                'continent' => 'OC',
                'variation' => '{"type": ["guam"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            90 => 
            array (
                'id' => 91,
                'code' => 'GW',
                'name' => 'Guinea-Bissau',
                'continent' => 'AF',
                'variation' => '{"type": ["guinea-bissau"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            91 => 
            array (
                'id' => 92,
                'code' => 'GY',
                'name' => 'Guyana',
                'continent' => 'SA',
                'variation' => '{"type": ["guyana"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            92 => 
            array (
                'id' => 93,
                'code' => 'HK',
                'name' => 'Hong Kong',
                'continent' => 'AS',
                'variation' => '{"type": ["hong kong"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:04',
            ),
            93 => 
            array (
                'id' => 94,
                'code' => 'HN',
                'name' => 'Honduras',
                'continent' => 'NA',
                'variation' => '{"type": ["honduras"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            94 => 
            array (
                'id' => 95,
                'code' => 'HR',
                'name' => 'Croatia',
                'continent' => 'EU',
                'variation' => '{"type": ["croatia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            95 => 
            array (
                'id' => 96,
                'code' => 'HT',
                'name' => 'Haiti',
                'continent' => 'NA',
                'variation' => '{"type": ["haiti"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            96 => 
            array (
                'id' => 97,
                'code' => 'HU',
                'name' => 'Hungary',
                'continent' => 'EU',
                'variation' => '{"type": ["hungary"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            97 => 
            array (
                'id' => 98,
                'code' => 'ID',
                'name' => 'Indonesia',
                'continent' => 'AS',
                'variation' => '{"type": ["indonesia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            98 => 
            array (
                'id' => 99,
                'code' => 'IE',
                'name' => 'Ireland',
                'continent' => 'EU',
                'variation' => '{"type": ["ireland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            99 => 
            array (
                'id' => 100,
                'code' => 'IL',
                'name' => 'Israel',
                'continent' => 'AS',
                'variation' => '{"type": ["israel"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            100 => 
            array (
                'id' => 101,
                'code' => 'IM',
                'name' => 'Isle of Man',
                'continent' => 'EU',
                'variation' => '{"type": ["isle of man"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            101 => 
            array (
                'id' => 102,
                'code' => 'IN',
                'name' => 'India',
                'continent' => 'AS',
                'variation' => '{"type": ["india"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            102 => 
            array (
                'id' => 103,
                'code' => 'IO',
                'name' => 'British Indian Ocean Territory',
                'continent' => 'AS',
                'variation' => '{"type": ["british indian ocean territory"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            103 => 
            array (
                'id' => 104,
                'code' => 'IQ',
                'name' => 'Iraq',
                'continent' => 'AS',
                'variation' => '{"type": ["iraq"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            104 => 
            array (
                'id' => 105,
                'code' => 'IR',
                'name' => 'Iran',
                'continent' => 'AS',
                'variation' => '{"type": ["iran"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            105 => 
            array (
                'id' => 106,
                'code' => 'IS',
                'name' => 'Iceland',
                'continent' => 'EU',
                'variation' => '{"type": ["iceland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            106 => 
            array (
                'id' => 107,
                'code' => 'IT',
                'name' => 'Italy',
                'continent' => 'EU',
                'variation' => '{"type": ["italy"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            107 => 
            array (
                'id' => 108,
                'code' => 'JE',
                'name' => 'Jersey',
                'continent' => 'EU',
                'variation' => '{"type": ["jersey"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            108 => 
            array (
                'id' => 109,
                'code' => 'JM',
                'name' => 'Jamaica',
                'continent' => 'NA',
                'variation' => '{"type": ["jamaica"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            109 => 
            array (
                'id' => 110,
                'code' => 'JO',
                'name' => 'Jordan',
                'continent' => 'AS',
                'variation' => '{"type": ["jordan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            110 => 
            array (
                'id' => 111,
                'code' => 'JP',
                'name' => 'Japan',
                'continent' => 'AS',
                'variation' => '{"type": ["japan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            111 => 
            array (
                'id' => 112,
                'code' => 'KE',
                'name' => 'Kenya',
                'continent' => 'AF',
                'variation' => '{"type": ["kenya"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            112 => 
            array (
                'id' => 113,
                'code' => 'KG',
                'name' => 'Kyrgyzstan',
                'continent' => 'AS',
                'variation' => '{"type": ["kyrgyzstan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            113 => 
            array (
                'id' => 114,
                'code' => 'KH',
                'name' => 'Cambodia',
                'continent' => 'AS',
                'variation' => '{"type": ["cambodia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            114 => 
            array (
                'id' => 115,
                'code' => 'KI',
                'name' => 'Kiribati',
                'continent' => 'OC',
                'variation' => '{"type": ["kiribati"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            115 => 
            array (
                'id' => 116,
                'code' => 'KM',
                'name' => 'Comoros',
                'continent' => 'AF',
                'variation' => '{"type": ["comoros"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            116 => 
            array (
                'id' => 117,
                'code' => 'KN',
                'name' => 'Saint Kitts and Nevis',
                'continent' => 'NA',
                'variation' => '{"type": ["saint kitts and nevis"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:05',
            ),
            117 => 
            array (
                'id' => 118,
                'code' => 'KP',
                'name' => 'North Korea',
                'continent' => 'AS',
                'variation' => '{"type": ["north korea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            118 => 
            array (
                'id' => 119,
                'code' => 'KR',
                'name' => 'South Korea',
                'continent' => 'AS',
                'variation' => '{"type": ["south korea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            119 => 
            array (
                'id' => 120,
                'code' => 'KW',
                'name' => 'Kuwait',
                'continent' => 'AS',
                'variation' => '{"type": ["kuwait"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            120 => 
            array (
                'id' => 121,
                'code' => 'KY',
                'name' => 'Cayman Islands',
                'continent' => 'NA',
                'variation' => '{"type": ["cayman islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            121 => 
            array (
                'id' => 122,
                'code' => 'KZ',
                'name' => 'Kazakhstan',
                'continent' => 'AS',
                'variation' => '{"type": ["kazakhstan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            122 => 
            array (
                'id' => 123,
                'code' => 'LA',
                'name' => 'Laos',
                'continent' => 'AS',
                'variation' => '{"type": ["laos"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            123 => 
            array (
                'id' => 124,
                'code' => 'LB',
                'name' => 'Lebanon',
                'continent' => 'AS',
                'variation' => '{"type": ["lebanon"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            124 => 
            array (
                'id' => 125,
                'code' => 'LC',
                'name' => 'Saint Lucia',
                'continent' => 'NA',
                'variation' => '{"type": ["saint lucia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            125 => 
            array (
                'id' => 126,
                'code' => 'LI',
                'name' => 'Liechtenstein',
                'continent' => 'EU',
                'variation' => '{"type": ["liechtenstein"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            126 => 
            array (
                'id' => 127,
                'code' => 'LK',
                'name' => 'Sri Lanka',
                'continent' => 'AS',
                'variation' => '{"type": ["sri lanka"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            127 => 
            array (
                'id' => 128,
                'code' => 'LR',
                'name' => 'Liberia',
                'continent' => 'AF',
                'variation' => '{"type": ["liberia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            128 => 
            array (
                'id' => 129,
                'code' => 'LS',
                'name' => 'Lesotho',
                'continent' => 'AF',
                'variation' => '{"type": ["lesotho"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            129 => 
            array (
                'id' => 130,
                'code' => 'LT',
                'name' => 'Lithuania',
                'continent' => 'EU',
                'variation' => '{"type": ["lithuania"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            130 => 
            array (
                'id' => 131,
                'code' => 'LU',
                'name' => 'Luxembourg',
                'continent' => 'EU',
                'variation' => '{"type": ["luxembourg"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            131 => 
            array (
                'id' => 132,
                'code' => 'LV',
                'name' => 'Latvia',
                'continent' => 'EU',
                'variation' => '{"type": ["latvia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            132 => 
            array (
                'id' => 133,
                'code' => 'LY',
                'name' => 'Libya',
                'continent' => 'AF',
                'variation' => '{"type": ["libya"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            133 => 
            array (
                'id' => 134,
                'code' => 'MA',
                'name' => 'Morocco',
                'continent' => 'AF',
                'variation' => '{"type": ["morocco"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            134 => 
            array (
                'id' => 135,
                'code' => 'MC',
                'name' => 'Monaco',
                'continent' => 'EU',
                'variation' => '{"type": ["monaco"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            135 => 
            array (
                'id' => 136,
                'code' => 'MD',
                'name' => 'Moldova',
                'continent' => 'EU',
                'variation' => '{"type": ["moldova"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            136 => 
            array (
                'id' => 137,
                'code' => 'ME',
                'name' => 'Montenegro',
                'continent' => 'EU',
                'variation' => '{"type": ["montenegro"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            137 => 
            array (
                'id' => 138,
                'code' => 'MF',
                'name' => 'Saint Martin',
                'continent' => 'NA',
                'variation' => '{"type": ["saint martin"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            138 => 
            array (
                'id' => 139,
                'code' => 'MG',
                'name' => 'Madagascar',
                'continent' => 'AF',
                'variation' => '{"type": ["madagascar"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:06',
            ),
            139 => 
            array (
                'id' => 140,
                'code' => 'MH',
                'name' => 'Marshall Islands',
                'continent' => 'OC',
                'variation' => '{"type": ["marshall islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            140 => 
            array (
                'id' => 141,
                'code' => 'MK',
                'name' => 'Macedonia',
                'continent' => 'EU',
                'variation' => '{"type": ["macedonia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            141 => 
            array (
                'id' => 142,
                'code' => 'ML',
                'name' => 'Mali',
                'continent' => 'AF',
                'variation' => '{"type": ["mali"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            142 => 
            array (
                'id' => 143,
                'code' => 'MM',
                'name' => 'Burma',
                'continent' => 'AS',
                'variation' => '{"type": ["burma"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            143 => 
            array (
                'id' => 144,
                'code' => 'MN',
                'name' => 'Mongolia',
                'continent' => 'AS',
                'variation' => '{"type": ["mongolia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            144 => 
            array (
                'id' => 145,
                'code' => 'MO',
                'name' => 'Macau',
                'continent' => 'AS',
                'variation' => '{"type": ["macau"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            145 => 
            array (
                'id' => 146,
                'code' => 'MP',
                'name' => 'Northern Mariana Islands',
                'continent' => 'OC',
                'variation' => '{"type": ["northern mariana islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            146 => 
            array (
                'id' => 147,
                'code' => 'MQ',
                'name' => 'Martinique',
                'continent' => 'NA',
                'variation' => '{"type": ["martinique"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            147 => 
            array (
                'id' => 148,
                'code' => 'MR',
                'name' => 'Mauritania',
                'continent' => 'AF',
                'variation' => '{"type": ["mauritania"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            148 => 
            array (
                'id' => 149,
                'code' => 'MS',
                'name' => 'Montserrat',
                'continent' => 'NA',
                'variation' => '{"type": ["montserrat"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            149 => 
            array (
                'id' => 150,
                'code' => 'MT',
                'name' => 'Malta',
                'continent' => 'EU',
                'variation' => '{"type": ["malta"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            150 => 
            array (
                'id' => 151,
                'code' => 'MU',
                'name' => 'Mauritius',
                'continent' => 'AF',
                'variation' => '{"type": ["mauritius"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            151 => 
            array (
                'id' => 152,
                'code' => 'MV',
                'name' => 'Maldives',
                'continent' => 'AS',
                'variation' => '{"type": ["maldives"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            152 => 
            array (
                'id' => 153,
                'code' => 'MW',
                'name' => 'Malawi',
                'continent' => 'AF',
                'variation' => '{"type": ["malawi"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            153 => 
            array (
                'id' => 154,
                'code' => 'MX',
                'name' => 'Mexico',
                'continent' => 'NA',
                'variation' => '{"type": ["mexico"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            154 => 
            array (
                'id' => 155,
                'code' => 'MY',
                'name' => 'Malaysia',
                'continent' => 'AS',
                'variation' => '{"type": ["malaysia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            155 => 
            array (
                'id' => 156,
                'code' => 'MZ',
                'name' => 'Mozambique',
                'continent' => 'AF',
                'variation' => '{"type": ["mozambique"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            156 => 
            array (
                'id' => 157,
                'code' => 'NA',
                'name' => 'Namibia',
                'continent' => 'AF',
                'variation' => '{"type": ["namibia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            157 => 
            array (
                'id' => 158,
                'code' => 'NC',
                'name' => 'New Caledonia',
                'continent' => 'OC',
                'variation' => '{"type": ["new caledonia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            158 => 
            array (
                'id' => 159,
                'code' => 'NE',
                'name' => 'Niger',
                'continent' => 'AF',
                'variation' => '{"type": ["niger"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            159 => 
            array (
                'id' => 160,
                'code' => 'NF',
                'name' => 'Norfolk Island',
                'continent' => 'OC',
                'variation' => '{"type": ["norfolk island"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            160 => 
            array (
                'id' => 161,
                'code' => 'NG',
                'name' => 'Nigeria',
                'continent' => 'AF',
                'variation' => '{"type": ["nigeria"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            161 => 
            array (
                'id' => 162,
                'code' => 'NI',
                'name' => 'Nicaragua',
                'continent' => 'NA',
                'variation' => '{"type": ["nicaragua"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:07',
            ),
            162 => 
            array (
                'id' => 163,
                'code' => 'NL',
                'name' => 'Netherlands',
                'continent' => 'EU',
                'variation' => '{"type": ["netherlands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            163 => 
            array (
                'id' => 164,
                'code' => 'NO',
                'name' => 'Norway',
                'continent' => 'EU',
                'variation' => '{"type": ["norway"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            164 => 
            array (
                'id' => 165,
                'code' => 'NP',
                'name' => 'Nepal',
                'continent' => 'AS',
                'variation' => '{"type": ["nepal"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            165 => 
            array (
                'id' => 166,
                'code' => 'NR',
                'name' => 'Nauru',
                'continent' => 'OC',
                'variation' => '{"type": ["nauru"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            166 => 
            array (
                'id' => 167,
                'code' => 'NU',
                'name' => 'Niue',
                'continent' => 'OC',
                'variation' => '{"type": ["niue"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            167 => 
            array (
                'id' => 168,
                'code' => 'NZ',
                'name' => 'New Zealand',
                'continent' => 'OC',
                'variation' => '{"type": ["new zealand"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            168 => 
            array (
                'id' => 169,
                'code' => 'OM',
                'name' => 'Oman',
                'continent' => 'AS',
                'variation' => '{"type": ["oman"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            169 => 
            array (
                'id' => 170,
                'code' => 'PA',
                'name' => 'Panama',
                'continent' => 'NA',
                'variation' => '{"type": ["panama"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            170 => 
            array (
                'id' => 171,
                'code' => 'PE',
                'name' => 'Perú',
                'continent' => 'SA',
                'variation' => '{"type": ["perú"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            171 => 
            array (
                'id' => 172,
                'code' => 'PF',
                'name' => 'French Polynesia',
                'continent' => 'OC',
                'variation' => '{"type": ["french polynesia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            172 => 
            array (
                'id' => 173,
                'code' => 'PG',
                'name' => 'Papua New Guinea',
                'continent' => 'OC',
                'variation' => '{"type": ["papua new guinea"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            173 => 
            array (
                'id' => 174,
                'code' => 'PH',
                'name' => 'Philippines',
                'continent' => 'AS',
                'variation' => '{"type": ["philippines"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            174 => 
            array (
                'id' => 175,
                'code' => 'PK',
                'name' => 'Pakistan',
                'continent' => 'AS',
                'variation' => '{"type": ["pakistan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            175 => 
            array (
                'id' => 176,
                'code' => 'PL',
                'name' => 'Poland',
                'continent' => 'EU',
                'variation' => '{"type": ["poland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            176 => 
            array (
                'id' => 177,
                'code' => 'PM',
                'name' => 'Saint Pierre and Miquelon',
                'continent' => 'NA',
                'variation' => '{"type": ["saint pierre and miquelon"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            177 => 
            array (
                'id' => 178,
                'code' => 'PN',
                'name' => 'Pitcairn',
                'continent' => 'OC',
                'variation' => '{"type": ["pitcairn"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            178 => 
            array (
                'id' => 179,
                'code' => 'PR',
                'name' => 'Puerto Rico',
                'continent' => 'NA',
                'variation' => '{"type": ["puerto rico"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            179 => 
            array (
                'id' => 180,
                'code' => 'PS',
                'name' => 'Palestinian Territory',
                'continent' => 'AS',
                'variation' => '{"type": ["palestinian territory"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            180 => 
            array (
                'id' => 181,
                'code' => 'PT',
                'name' => 'Portugal',
                'continent' => 'EU',
                'variation' => '{"type": ["portugal"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            181 => 
            array (
                'id' => 182,
                'code' => 'PW',
                'name' => 'Palau',
                'continent' => 'OC',
                'variation' => '{"type": ["palau"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            182 => 
            array (
                'id' => 183,
                'code' => 'PY',
                'name' => 'Paraguay',
                'continent' => 'SA',
                'variation' => '{"type": ["paraguay"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            183 => 
            array (
                'id' => 184,
                'code' => 'QA',
                'name' => 'Qatar',
                'continent' => 'AS',
                'variation' => '{"type": ["qatar"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            184 => 
            array (
                'id' => 185,
                'code' => 'RE',
                'name' => 'Réunion',
                'continent' => 'AF',
                'variation' => '{"type": ["réunion"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            185 => 
            array (
                'id' => 186,
                'code' => 'RO',
                'name' => 'Romania',
                'continent' => 'EU',
                'variation' => '{"type": ["romania"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            186 => 
            array (
                'id' => 187,
                'code' => 'RS',
                'name' => 'Serbia',
                'continent' => 'EU',
                'variation' => '{"type": ["serbia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:08',
            ),
            187 => 
            array (
                'id' => 188,
                'code' => 'RU',
                'name' => 'Russia',
                'continent' => 'EU',
                'variation' => '{"type": ["russia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            188 => 
            array (
                'id' => 189,
                'code' => 'RW',
                'name' => 'Rwanda',
                'continent' => 'AF',
                'variation' => '{"type": ["rwanda"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            189 => 
            array (
                'id' => 190,
                'code' => 'SA',
                'name' => 'Saudi Arabia',
                'continent' => 'AS',
                'variation' => '{"type": ["saudi arabia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            190 => 
            array (
                'id' => 191,
                'code' => 'SB',
                'name' => 'Solomon Islands',
                'continent' => 'OC',
                'variation' => '{"type": ["solomon islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            191 => 
            array (
                'id' => 192,
                'code' => 'SC',
                'name' => 'Seychelles',
                'continent' => 'AF',
                'variation' => '{"type": ["seychelles"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            192 => 
            array (
                'id' => 193,
                'code' => 'SD',
                'name' => 'Sudan',
                'continent' => 'AF',
                'variation' => '{"type": ["sudan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            193 => 
            array (
                'id' => 194,
                'code' => 'SE',
                'name' => 'Sweden',
                'continent' => 'EU',
                'variation' => '{"type": ["sweden"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            194 => 
            array (
                'id' => 195,
                'code' => 'SG',
                'name' => 'Singapore',
                'continent' => 'AS',
                'variation' => '{"type": ["singapore"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            195 => 
            array (
                'id' => 196,
                'code' => 'SH',
                'name' => 'Saint Helena',
                'continent' => 'AF',
                'variation' => '{"type": ["saint helena"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            196 => 
            array (
                'id' => 197,
                'code' => 'SI',
                'name' => 'Slovenia',
                'continent' => 'EU',
                'variation' => '{"type": ["slovenia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            197 => 
            array (
                'id' => 198,
                'code' => 'SK',
                'name' => 'Slovakia',
                'continent' => 'EU',
                'variation' => '{"type": ["slovakia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            198 => 
            array (
                'id' => 199,
                'code' => 'SL',
                'name' => 'Sierra Leone',
                'continent' => 'AF',
                'variation' => '{"type": ["sierra leone"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            199 => 
            array (
                'id' => 200,
                'code' => 'SM',
                'name' => 'San Marino',
                'continent' => 'EU',
                'variation' => '{"type": ["san marino"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            200 => 
            array (
                'id' => 201,
                'code' => 'SN',
                'name' => 'Senegal',
                'continent' => 'AF',
                'variation' => '{"type": ["senegal"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            201 => 
            array (
                'id' => 202,
                'code' => 'SO',
                'name' => 'Somalia',
                'continent' => 'AF',
                'variation' => '{"type": ["somalia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            202 => 
            array (
                'id' => 203,
                'code' => 'SR',
                'name' => 'Suriname',
                'continent' => 'SA',
                'variation' => '{"type": ["suriname"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            203 => 
            array (
                'id' => 204,
                'code' => 'SS',
                'name' => 'South Sudan',
                'continent' => 'AF',
                'variation' => '{"type": ["south sudan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            204 => 
            array (
                'id' => 205,
                'code' => 'ST',
                'name' => 'São Tomé and Principe',
                'continent' => 'AF',
                'variation' => '{"type": ["são tomé and principe"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            205 => 
            array (
                'id' => 206,
                'code' => 'SV',
                'name' => 'El Salvador',
                'continent' => 'NA',
                'variation' => '{"type": ["el salvador"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            206 => 
            array (
                'id' => 207,
                'code' => 'SX',
                'name' => 'Sint Maarten',
                'continent' => 'NA',
                'variation' => '{"type": ["sint maarten"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            207 => 
            array (
                'id' => 208,
                'code' => 'SY',
                'name' => 'Syria',
                'continent' => 'AS',
                'variation' => '{"type": ["syria"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            208 => 
            array (
                'id' => 209,
                'code' => 'SZ',
                'name' => 'Swaziland',
                'continent' => 'AF',
                'variation' => '{"type": ["swaziland"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:09',
            ),
            209 => 
            array (
                'id' => 210,
                'code' => 'TC',
                'name' => 'Turks and Caicos Islands',
                'continent' => 'NA',
                'variation' => '{"type": ["turks and caicos islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            210 => 
            array (
                'id' => 211,
                'code' => 'TD',
                'name' => 'Chad',
                'continent' => 'AF',
                'variation' => '{"type": ["chad"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            211 => 
            array (
                'id' => 212,
                'code' => 'TF',
                'name' => 'French Southern Territories',
                'continent' => 'AN',
                'variation' => '{"type": ["french southern territories"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            212 => 
            array (
                'id' => 213,
                'code' => 'TG',
                'name' => 'Togo',
                'continent' => 'AF',
                'variation' => '{"type": ["togo"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            213 => 
            array (
                'id' => 214,
                'code' => 'TH',
                'name' => 'Thailand',
                'continent' => 'AS',
                'variation' => '{"type": ["thailand"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            214 => 
            array (
                'id' => 215,
                'code' => 'TJ',
                'name' => 'Tajikistan',
                'continent' => 'AS',
                'variation' => '{"type": ["tajikistan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            215 => 
            array (
                'id' => 216,
                'code' => 'TK',
                'name' => 'Tokelau',
                'continent' => 'OC',
                'variation' => '{"type": ["tokelau"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            216 => 
            array (
                'id' => 217,
                'code' => 'TL',
                'name' => 'Timor-Leste',
                'continent' => 'AS',
                'variation' => '{"type": ["timor-leste"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            217 => 
            array (
                'id' => 218,
                'code' => 'TM',
                'name' => 'Turkmenistan',
                'continent' => 'AS',
                'variation' => '{"type": ["turkmenistan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            218 => 
            array (
                'id' => 219,
                'code' => 'TN',
                'name' => 'Tunisia',
                'continent' => 'AF',
                'variation' => '{"type": ["tunisia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            219 => 
            array (
                'id' => 220,
                'code' => 'TO',
                'name' => 'Tonga',
                'continent' => 'OC',
                'variation' => '{"type": ["tonga"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            220 => 
            array (
                'id' => 221,
                'code' => 'TR',
                'name' => 'Turkey',
                'continent' => 'AS',
                'variation' => '{"type": ["turkey"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            221 => 
            array (
                'id' => 222,
                'code' => 'TT',
                'name' => 'Trinidad and Tobago',
                'continent' => 'NA',
                'variation' => '{"type": ["trinidad and tobago"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            222 => 
            array (
                'id' => 223,
                'code' => 'TV',
                'name' => 'Tuvalu',
                'continent' => 'OC',
                'variation' => '{"type": ["tuvalu"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            223 => 
            array (
                'id' => 224,
                'code' => 'TW',
                'name' => 'Taiwan',
                'continent' => 'AS',
                'variation' => '{"type": ["taiwan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            224 => 
            array (
                'id' => 225,
                'code' => 'TZ',
                'name' => 'Tanzania',
                'continent' => 'AF',
                'variation' => '{"type": ["tanzania"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:10',
            ),
            225 => 
            array (
                'id' => 226,
                'code' => 'UA',
                'name' => 'Ukraine',
                'continent' => 'EU',
                'variation' => '{"type": ["ukraine"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            226 => 
            array (
                'id' => 227,
                'code' => 'UG',
                'name' => 'Uganda',
                'continent' => 'AF',
                'variation' => '{"type": ["uganda"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            227 => 
            array (
                'id' => 228,
                'code' => 'UM',
                'name' => 'United States Minor Outlying Islands',
                'continent' => 'OC',
                'variation' => '{"type": ["united states minor outlying islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            228 => 
            array (
                'id' => 229,
                'code' => 'US',
                'name' => 'United States',
                'continent' => 'NA',
                'variation' => '{"type": ["united states"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            229 => 
            array (
                'id' => 230,
                'code' => 'UY',
                'name' => 'Uruguay',
                'continent' => 'SA',
                'variation' => '{"type": ["uruguay"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            230 => 
            array (
                'id' => 231,
                'code' => 'UZ',
                'name' => 'Uzbekistan',
                'continent' => 'AS',
                'variation' => '{"type": ["uzbekistan"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            231 => 
            array (
                'id' => 232,
                'code' => 'VA',
                'name' => 'Vatican City',
                'continent' => 'EU',
                'variation' => '{"type": ["vatican city"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            232 => 
            array (
                'id' => 233,
                'code' => 'VC',
                'name' => 'Saint Vincent and the Grenadines',
                'continent' => 'NA',
                'variation' => '{"type": ["saint vincent and the grenadines"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            233 => 
            array (
                'id' => 234,
                'code' => 'VE',
                'name' => 'Venezuela',
                'continent' => 'SA',
                'variation' => '{"type": ["venezuela"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            234 => 
            array (
                'id' => 235,
                'code' => 'VG',
                'name' => 'British Virgin Islands',
                'continent' => 'NA',
                'variation' => '{"type": ["british virgin islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            235 => 
            array (
                'id' => 236,
                'code' => 'VI',
                'name' => 'U.S. Virgin Islands',
                'continent' => 'NA',
                'variation' => '{"type": ["u.s. virgin islands"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            236 => 
            array (
                'id' => 237,
                'code' => 'VN',
                'name' => 'Vietnam',
                'continent' => 'AS',
                'variation' => '{"type": ["vietnam"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            237 => 
            array (
                'id' => 238,
                'code' => 'VU',
                'name' => 'Vanuatu',
                'continent' => 'OC',
                'variation' => '{"type": ["vanuatu"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            238 => 
            array (
                'id' => 239,
                'code' => 'WF',
                'name' => 'Wallis and Futuna',
                'continent' => 'OC',
                'variation' => '{"type": ["wallis and futuna"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            239 => 
            array (
                'id' => 240,
                'code' => 'WS',
                'name' => 'Samoa',
                'continent' => 'OC',
                'variation' => '{"type": ["samoa"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            240 => 
            array (
                'id' => 241,
                'code' => 'XK',
                'name' => 'Kosovo',
                'continent' => 'EU',
                'variation' => '{"type": ["kosovo"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            241 => 
            array (
                'id' => 242,
                'code' => 'YE',
                'name' => 'Yemen',
                'continent' => 'AS',
                'variation' => '{"type": ["yemen"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            242 => 
            array (
                'id' => 243,
                'code' => 'YT',
                'name' => 'Mayotte',
                'continent' => 'AF',
                'variation' => '{"type": ["mayotte"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            243 => 
            array (
                'id' => 244,
                'code' => 'ZA',
                'name' => 'South Africa',
                'continent' => 'AF',
                'variation' => '{"type": ["south africa"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:11',
            ),
            244 => 
            array (
                'id' => 245,
                'code' => 'ZM',
                'name' => 'Zambia',
                'continent' => 'AF',
                'variation' => '{"type": ["zambia"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
            245 => 
            array (
                'id' => 246,
                'code' => 'ZW',
                'name' => 'Zimbabwe',
                'continent' => 'AF',
                'variation' => '{"type": ["zimbabwe"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
            246 => 
            array (
                'id' => 247,
                'code' => 'ZZ',
                'name' => 'Unknown or unassigned country',
                'continent' => 'AF',
                'variation' => '{"type": ["unknown or unassigned country"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
            247 => 
            array (
                'id' => 248,
                'code' => 'PPP',
                'name' => 'No Existe',
                'continent' => 'N/A',
                'variation' => '{"type": ["no existe"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
            248 => 
            array (
                'id' => 249,
                'code' => 'MM',
                'name' => 'Myanmar',
                'continent' => 'Asia',
                'variation' => '{"type": ["myanmar"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
            249 => 
            array (
                'id' => 250,
                'code' => 'ALL',
                'name' => 'ALL',
                'continent' => 'ALL',
                'variation' => '{"type": ["all"]}',
                'created_at' => NULL,
                'updated_at' => '2019-03-14 13:20:12',
            ),
        ));
        
        
    }
}