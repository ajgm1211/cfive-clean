<?php

use Faker\Generator as Faker;

$factory->define(App\QuoteV2::class, function (Faker $faker) {
    return [
        'company_user_id' => 1,
        'quote_id' => 'CA-testluis',
        'type' => 'FCL',
        'validity_start' => date('Y-m-d'),
        'validity_end' => date('Y-m-d'),
        'equipment' => "[\"1\",\"2\",\"3\"]",
        'delivery_type' => 1,
        'user_id' => 1,
        'date_issued' => date('Y-m-d'),
        'status' => 'Draft',
    ];
});
