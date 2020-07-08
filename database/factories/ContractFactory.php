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

$factory->define(App\Contract::class, function (Faker $faker) {
    return [
    	// 'id'=>1,
        'name' => $faker->company,
        'status' => 'publish',
        'validity' => "2020-07-10",
        'expire' => "2020-10-22 ",

    ];
});
