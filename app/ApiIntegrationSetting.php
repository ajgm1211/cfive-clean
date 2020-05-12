<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiIntegrationSetting extends Model
{
    protected $fillable = ['company_user_id',
                           'api_key',
                           'api_integration_id',
                           'enable'
                          ];
}
