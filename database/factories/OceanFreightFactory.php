<?php

use Faker\Generator as Faker;

$factory->define(App\Rate::class, function (Faker $faker) {
    return [
         
        'origin_port'=>946,
        'destiny_port'=>986,
        'carrier_id'=>1,
        'contract_id'=>1,
        'twuenty'=>150,
        'forty'=>300,
        'fortyhc'=>300,
        'fortynor'=>300,
        'fortyfive'=>600,
        'currency_id'=>3,
    ];
});