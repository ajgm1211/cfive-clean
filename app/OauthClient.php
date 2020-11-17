<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{

    protected $fillable = ['user_id','company_user_id','name','secret','redirect','personal_access_client','password_client','revoked'];

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser','company_user_id','id');
    }
}
