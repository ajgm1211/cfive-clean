<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\MailResetPasswordNotification as MailResetPasswordNotification;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{

  use Notifiable;
  use HasRoles;
  use \OwenIt\Auditing\Auditable;

  protected $fillable = [
    'id','name','lastname', 'password', 'email','phone','type','company_user_id','position','verified','access'
  ];

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
  public function NewContractRequests(){
    return $this->hasMany('App\NewContractRequest');
  }
  /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
  public function sendPasswordResetNotification($token)
  {
    $this->notify(new MailResetPasswordNotification($token));
  }
}
