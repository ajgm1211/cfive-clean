<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class NewContractRequest extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $table = 'newcontractrequests';
    protected $fillable = ['namecontract',
                           'numbercontract',
                           'validation',
                           'direction_id',
                           'company_user_id',
                           'namefile',
                           'user_id',
                           'created',
                           'created_at',
                           'time_star',
                           'time_total',
                           'time_manager',
                           'time_star_one',
                           'sentemail',
                           'contract_id',
                           'type',
                           'data'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function direction(){
        return $this->belongsTo('App\Direction');
    }

    public function Requestcarriers(){
        return $this->hasMany('App\RequetsCarrierFcl','request_id');
    }

    public function companyuser(){
        return $this->belongsTo('App\CompanyUser','company_user_id');
    }
}
