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
        'id', 'name', 'lastname', 'password', 'email', 'phone', 'type', 'company_user_id', 'position', 'verified', 'access',
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
}
