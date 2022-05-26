<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class QuoteSegmentType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['id','name'];

    public function companyUserQuoteSegment(){
        return $this->hasMany('App\CompanyUserQuoteSegment');
    }
}