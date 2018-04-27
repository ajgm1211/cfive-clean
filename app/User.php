<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','lastname', 'password', 'email', 'type','name_company','position',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function subuser(){

        return $this->hasOne('App\Subuser');

    }
    public function contracts(){

        return $this->hasMany('App\Contract');

    }
    public function surcharges(){

        return $this->hasMany('App\Surcharge');

    }


}
