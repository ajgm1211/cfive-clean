<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'options' => 'array',
    ];

    protected $fillable = ['id','name', 'address', 'phone', 'currency_id', 'logo', 'pdf_language', 'type_pdf', 'pdf_ammounts', 'decimals'];

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

    public function quota()
    {
        return $this->hasOne('App\QuotaRequest');
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

    public function settingsWhitelabel()
    {
        return $this->hasOne('App\SettingsWhitelabel');
    }
 
    
    public function companyUserQuoteSegment(){
        return $this->hasMany('App\CompanyUserQuoteSegment');
    }


   /*  public static function boot()
    {
        parent::boot();

        //created or creating or updated
        self::updating(function($companyUser){
            
            $options = $companyUser->options;
            
            $options['disable_delegation_pdf'] = false;
            //dd('hola', $options , $companyUser['options']);
            $companyUser->update(['options' => $options]);
            $companyUser['options'] = $options;
            $companyUser->update();
        });
            
        
    } */
}
