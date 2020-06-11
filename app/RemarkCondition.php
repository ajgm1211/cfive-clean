<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemarkCondition extends Model
{
    protected $table = "remark_conditions";
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'import',
        'export',
        'company_user_id',
        'language_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function remarksCountries()
    {
        return $this->HasManyThrough('App\Country', 'App\RemarkCountry', 'remark_condition_id', 'id', 'id', 'country_id');
    }

    public function remarksCarriers()
    {
        return $this->HasManyThrough('App\Carrier', 'App\RemarkCarrier', 'remark_condition_id', 'id', 'id', 'carrier_id');
    }

    public function remarksHarbors()
    {
        return $this->HasManyThrough('App\Harbor', 'App\RemarkHarbor', 'remark_condition_id', 'id', 'id', 'port_id');
    }

    public function language()
    {
        return $this->belongsTo('App\Language', 'language_id');
    }
}
