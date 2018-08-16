<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
  use Notifiable;
  use HasRoles;
  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */




  protected $fillable = [
    'id','name','lastname', 'password', 'email', 'type','company_user_id','position','verified','access'
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
  public function emailsTemplates(){

    return $this->hasMany('App\EmailTemplate');

  }

  public function verifyUser()
  {
    return $this->hasOne('App\VerifyUser');
  }

  public function companyUser()
  {
    return $this->belongsTo('App\CompanyUser');
  }


}
