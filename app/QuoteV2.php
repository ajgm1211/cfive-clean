<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteV2 extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'equipment' => 'array',
    ];

    protected $fillable = ['company_user_id','quote_id','type','quote_validity','validity_start','validity_end','origin_address','destination_address','company_id','contact_id','delivery_type','user_id','equipment','incoterm_id','status','date_issued','price_id','total_quantity','total_weight','total_volume','chargeable_weight','cargo_type','kind_of_cargo','commodity','payment_conditions'];

    public function company()
    {
        return $this->hasOne('App\Company','id','company_id');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function origin_port()
    {
        return $this->hasOne('App\Harbor','id','origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor','id','destination_port_id');
    }

    public function incoterm()
    {
        return $this->hasOne('App\Incoterm','id','incoterm_id');
    }

    public function price()
    {
        return $this->hasOne('App\Price','id','price_id');
    }

    public function payment()
    {
        return $this->hasMany('App\PaymentCondition','id','quote_id');
    }

    /*public function terms()
    {
        return $this->hasMany('App\TermsAndCondition','id','quote_id');
    }*/

    public function rate()
    {
        return $this->hasOne('App\AutomaticRate','quote_id','id');
    }

    public function rates_v2()
    {
        return $this->hasMany('App\AutomaticRate','quote_id','id');
    }

    public function charge()
    {
        return $this->hasManyThrough('App\Charge','App\AutomaticRate','quote_id','automatic_rate_id');
    }

    public function pdf_option()
    {
        return $this->hasOne('App\PdfOption','quote_id','id');
    }    

    public function packing_load()
    {
        return $this->hasOne('App\PackageLoadV2','quote_id','id');
    }

    public function integration()
    {
        return $this->hasOne('App\IntegrationQuoteStatus','quote_id','id');
    }

    public function scopeExclude($query,$value = array()) 
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }
}
