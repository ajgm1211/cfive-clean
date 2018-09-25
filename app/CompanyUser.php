<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $fillable = ['name','address','phone','currency_id', 'logo','pdf_language'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
}
