<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['owner', 'company_id','origin','destination','ammount','status_id'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function user()
    {
        return $this->belongsTo('App\User','owner','id');
    }

    public function origin_country()
    {
        return $this->belongsTo('App\Country','origin','id');
    }

    public function destination_country()
    {
        return $this->belongsTo('App\Country','destination','id');
    }
}
