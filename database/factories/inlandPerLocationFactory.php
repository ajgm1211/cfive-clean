<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\InlandPerLocation::class, function (Faker $faker) {
    $array=[
        'C20DV'=>30,
        'C40DV'=>11,
        'C40HC'=>90,
    ];
    return [
        'json_containers'=>json_encode($array), 
        'currency_id'=>$faker->numberBetween($min = 1, $max = 167),
        'harbor_id'=>$faker->numberBetween($min = 743, $max = 1000), 
        'inland_id'=>$faker->numberBetween($min = 22, $max = 31), 
        'location_id'=>$faker->numberBetween($min = 1, $max = 4), 
        'service_id'=>$faker->numberBetween($min = 1, $max = 3),
    ];
});