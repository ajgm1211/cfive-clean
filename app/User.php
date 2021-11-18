<?php

namespace App;

use App\Notifications\MailResetPasswordNotification as MailResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, Notifiable;
    use HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'id', 'name', 'lastname', 'password', 'email', 'phone', 'type', 'company_user_id', 'position', 'verified', 'access', 'api_token', 'whitelabel',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function subuser()
    {
        return $this->hasOne('App\Subuser');
    }

    public function contracts()
    {
        return $this->hasMany('App\Contract');
    }

    public function surcharges()
    {
        return $this->hasMany('App\Surcharge');
    }

    public function emailsTemplates()
    {
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
    public function delegation()
    {
        return $this->belongsToMany('App\Delegation');
    }

    public function userToken()
    {
        return $this->hasOne('App\OauthAccessToken');
    }

    public function NewContractRequests()
    {
        return $this->hasMany('App\NewContractRequest');
    }

    public function userConfiguration()
    {
        return $this->hasOne('App\UserConfiguration');
    }

    public function scopeIsAdmin($query)
    {
        return $query->where('type', 'admin');
    }

    public function worksAt()
    {
        return $this->hasOne('App\CompanyUser','id','company_user_id')->first();
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

    public function routeNotificationForSlack()
    {
        return 'https://hooks.slack.com/services/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';
    }
    public function setPasswordAttribute($password)
    {   
        if (!empty($password))
        {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->lastname}";
    }

    public function storeDelegation($delegation_id,$user_id){

        $delegation= new UserDelegation();
        $delegation->users_id=$user_id;
        $delegation->delegations_id=$delegation_id;
        $delegation->save();
        
     }

     public function settingsWhitelabel()
     {
         return $this->hasOne('App\SettingsWhitelabel');
     }
}
