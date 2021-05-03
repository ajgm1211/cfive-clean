<?php

use Faker\Generator as Faker;

$factory->define(App\SaleTermV3::class, function (Faker $faker) {
    return [
         
        'name'=>$faker->name,
        'company_user_id' => 1,
        'type_id'=>1,
        'port_id'=>$faker->numberBetween($min = 800, $max = 1700), 
        'group_container_id'=>2,

    ];
});