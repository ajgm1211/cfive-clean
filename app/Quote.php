<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Quote extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['owner','company_user_id','company_quote','incoterm','modality','validity','origin_address','destination_address','company_id','origin_harbor_id','destination_harbor_id','origin_airport_id','destination_airport_id','price_id','contact_id','qty_20','qty_40','qty_40_hc','qty_45_hc','qty_40_nor','qty_20_reefer','qty_40_reefer','qty_40_hc_reefer','qty_20_open_top','qty_40_open_top','qty_40_hc_open_top','total_quantity','total_weight','total_volume','type_cargo','status_quote_id','pick_up_date','delivery_type','currency_id','type','sub_total_origin','sub_total_freight','sub_total_destination','total_markup_origin','total_markup_freight','total_markup_destination','sale_term_id','carrier_id','airline_id','term_orig','term_dest','term','pdf_language','payment_conditions','since_validity','chargeable_weight','hide_carrier','contract_number','custom_id'];

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function incoterm()
    {
        return $this->belongsTo('App\Incoterm','id','incoterm');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function airline()
    {
        return $this->belongsTo('App\Airline');
    }

    public function user()
    {
        return $this->belongsTo('App\User','owner','id');
    }

    public function price()
    {
        return $this->belongsTo('App\Price');
    }

    public function status()
    {
        return $this->hasOne('App\StatusQuote','id','status_quote_id');
    }

    public function sale_term()
    {
        return $this->hasOne('App\SaleTerm','id','sale_term_id');
    }

    public function origin_harbor()
    {
        return $this->hasOne('App\Harbor','id','origin_harbor_id');
    }

    public function destination_harbor()
    {
        return $this->hasOne('App\Harbor','id','destination_harbor_id');
    }

    public function origin_airport()
    {
        return $this->hasOne('App\Airport','id','origin_airport_id');
    }

    public function destination_airport()
    {
        return $this->hasOne('App\Airport','id','destination_airport_id');
    }    

    public function company_name()
    {
        return $this->hasManyThrough('App\Company','App\CompanyPrice','price_id','id','id','company_id');
    }
    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
    public function packages()
    {
        return $this->hasMany('App\PackageLoad','quote_id');
    }
    public function currencies()
    {
        return $this->hasOne('App\Currency','id','currency_id');
    }
    public function freightAmmount()
    {
        return $this->hasMany('App\FreightAmmount','quote_id');
    }
    public function originAmmount()
    {
        return $this->hasMany('App\OriginAmmount','quote_id');
    }
    public function destinationAmmount()
    {
        return $this->hasMany('App\DestinationAmmount','quote_id');
    }
}
