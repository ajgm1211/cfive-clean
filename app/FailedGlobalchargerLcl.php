<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailedGlobalchargerLcl extends Model
{
    protected $table = 'failed_globalcharger_lcl';
    protected $fillable = ['surcharge',
                           'origin',
                           'destiny',
                           'typedestiny',
                           'calculationtypelcl',
                           'ammount',
                           'minimum',
                           'validity',
                           'expire',
                           'currency',
                           'port',
                           'country',
                           'carrier',
                           'company_user_id',
                           'account_imp_gclcl_id',
                           'differentiator'
                          ];








}
