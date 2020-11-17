<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemarkCountry extends Model
{
    protected $fillable = ['country_id', 'remark_condition_id'];


    public function remark()
    {
        return $this->belongsTo('App\RemarkCondition', 'remark_condition_id');
    }
    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }
}
