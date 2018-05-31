<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $fillable = ['name','address','phone','currency_id'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function company()
    {
        return $this->belongsTo('App\Currency');
    }
}
