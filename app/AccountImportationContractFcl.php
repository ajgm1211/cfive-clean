<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class AccountImportationContractFcl extends Model implements HasMedia, Auditable
{
    use HasMediaTrait;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'accounts_import_cfcl';
    protected $fillable = ['id',
                           'name',
                           'date',
                           'namefile',
                           'company_user_id',
                           'request_id',
                          ];

    public function companyuser()
    {
        return $this->belongsTo('App\CompanyUser', 'company_user_id');
    }

    public function FilesTmps()
    {
        return $this->hasMany('App\FileTmp');
    }

    public function contract()
    {
        return $this->hasOne('App\Contract', 'account_id');
    }
}
