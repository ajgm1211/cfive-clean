<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingsWhitelabel extends Model
{
    protected $fillable = ['url', 'company_user_id','user_id'];


    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
