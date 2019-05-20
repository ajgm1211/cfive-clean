<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{

    protected $fillable = [
        'id','user_id','client_id', 'name', 'scopes','revoked'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
