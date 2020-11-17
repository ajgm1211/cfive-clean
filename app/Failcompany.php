<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Failcompany extends Model
{
    protected $table    = "fail_companies";
    protected $fillable = ['business_name',
                           'phone',
                           'address',
                           'email',
                           'tax_number',
                           'logo',
                           'associated_quotes',
                           'company_user_id',
                           'owner'
                          ];
}
