<?php

use Faker\Generator as Faker;

$factory->define(App\Company::class, function (Faker $faker) {
    return [
        'business_name' => $faker->company,
        'phone' => $faker->phoneNumber,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->address,
        'owner' => 1,
        'company_user_id' => 1,
    ];
});
