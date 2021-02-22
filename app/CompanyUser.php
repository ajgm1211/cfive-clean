<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'address', 'phone', 'currency_id', 'logo', 'pdf_language', 'type_pdf', 'pdf_ammounts', 'decimals'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function language()
    {
        return $this->hasOne('App\Language','id','pdf_language');
    }

    public function companyQuotes()
    {
        return $this->hasMany('App\QuoteV2');
    }

    public function getHigherId($companyCode)
    {
        $ids = [];
        $quotes = $this->companyQuotes()->get();
        if(count($quotes)==0){
            return count($quotes);
        } else {
            foreach($quotes as $q){
                $qid = intval(str_replace($companyCode."-","",$q->quote_id));
                array_push($ids,$qid);
            }
        }
        
        return max($ids);
    }
}
