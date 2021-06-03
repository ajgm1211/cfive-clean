<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandPerLocation extends Model
{
    protected $table = 'inland_location';
    protected $fillable = ['json_container, currency_id, harbor_id, inland_id, location_id, type_id'];

    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function harbor()
    {
        return $this->belongsTo('App\harbors', 'harbor_id');
    }

    public function location()
    {
        return $this->belongsTo('App\location', 'location_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\currency', 'currency_id');
    }

}