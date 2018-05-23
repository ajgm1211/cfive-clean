<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['first_name','last_name','phone','email','company_id'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }
}
