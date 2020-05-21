<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'api_integration_setting_id',
        'url'
    ];

    public function api_integration_setting(){
        return $this->belongsTo('App\ApiIntegrationSetting');
    }

    public function partner(){
        return $this->belongsTo('App\Partner');
    }
}
