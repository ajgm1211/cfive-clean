<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthtokenToken extends Model
{
    protected $table = 'authtoken_token';
    protected $fillable = ['key',
                           'user_id'
                          ];
}
