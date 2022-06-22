<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Carrier extends Model
{
    use Rememberable;
    protected $table = 'carriers';
    protected $fillable = ['id', 'name', 'image', 'varation'];

    public function rate()
    {
        return $this->hasOne('App\Rate');
    }

    public function automatic_rate()
    {
        return $this->hasOne('App\AutomaticRate');
    }

    public function globalcharge()
    {
        return $this->hasOne('App\GlobalCharge');
    }

    public function globalcharcarrier()
    {
        return $this->hasMany('App\GlobalCharPortCarrier');
    }

    public function globalcharport()
    {
        return $this->hasMany('App\GlobalCharPortCarrier');
    }

    public function getUrlAttribute($value)
    {
        return config('medialibrary.s3.domain') . "/imgcarrier/" . $value;
    }

    public function search_carriers()
    {
        return $this->morphToMany(SearchCarrier::class, 'provider', 'provider_type', 'provider_id');
    }

    public function referentialData($company_user_id)
    {
        return $this->morphOne('App\ReferentialData', 'referential')
            ->where('company_user_id', $company_user_id)
            ->first();
    }
}
