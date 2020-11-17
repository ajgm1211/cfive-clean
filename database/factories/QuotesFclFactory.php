<?php

use Faker\Generator as Faker;

$factory->define(App\QuoteV2::class, function (Faker $faker) {
    return [
         
  'company_user_id'=>1,
  'quote_id'=>'CA-14',
  'type'=>'FCL',
  'validity_start'=>'2020-10-20',
  'validity_end'=>'2020-10-20',
  'equipment'=>"[\"1\",\"2\",\"3\"]",
  'delivery_type'=>1,
  'user_id'=>1,
  'date_issued'=>'2020-10-20',
  'status'=>'Draft',
       
    ];
});