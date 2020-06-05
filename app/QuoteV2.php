<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class QuoteV2 extends Model  implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'equipment' => 'array',
    ];

    protected $fillable = ['company_user_id', 'quote_id', 'type', 'quote_validity', 'validity_start', 'validity_end', 'origin_address', 'destination_address', 'company_id', 'contact_id', 'delivery_type', 'user_id', 'equipment', 'incoterm_id', 'status', 'date_issued', 'price_id', 'total_quantity', 'total_weight', 'total_volume', 'chargeable_weight', 'cargo_type', 'kind_of_cargo', 'commodity', 'payment_conditions'];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
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
        return $this->hasOne('App\Harbor', 'id', 'origin_port_id');
    }

    public function destination_port()
    {
        return $this->hasOne('App\Harbor', 'id', 'destination_port_id');
    }

    public function incoterm()
    {
        return $this->hasOne('App\Incoterm', 'id', 'incoterm_id');
    }

    public function price()
    {
        return $this->hasOne('App\Price', 'id', 'price_id');
    }

    public function payment()
    {
        return $this->hasMany('App\PaymentCondition', 'id', 'quote_id');
    }

    /*public function terms()
    {
        return $this->hasMany('App\TermsAndCondition','id','quote_id');
    }*/

    public function rate()
    {
        return $this->hasOne('App\AutomaticRate', 'quote_id', 'id');
    }

    public function saleterm()
    {
        return $this->hasOne('App\SaleTermV2', 'quote_id', 'id');
    }

    public function rates_v2()
    {
        return $this->hasMany('App\AutomaticRate', 'quote_id', 'id');
    }

    public function charge()
    {
        return $this->hasManyThrough('App\Charge', 'App\AutomaticRate', 'quote_id', 'automatic_rate_id');
    }

    public function pdf_option()
    {
        return $this->hasOne('App\PdfOption', 'quote_id', 'id');
    }

    public function packing_load()
    {
        return $this->hasOne('App\PackageLoadV2', 'quote_id', 'id');
    }

    public function integration()
    {
        return $this->hasOne('App\IntegrationQuoteStatus', 'quote_id', 'id');
    }

    public function scopeExclude($query, $value = array())
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }

    /*public function getEquipmentAttribute($value) 
    {
        $a = json_decode($value);
        return json_decode($a);
    }*/

    public function scopeUserRelation($q)
    {
        return $q->with(['user' => function ($query) {
            $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company as company_name', 'company_user_id');
            $query->with(['companyUser' => function ($q) {
                $q->select('id', 'name', 'address', 'phone', 'currency_id');
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode');
                }]);
            }]);
        }]);
    }

    public function scopeCompanyRelation($q)
    {
        return $q->with(['company' => function ($query) {
            $query->with(['company_user' => function ($q) {
                $q->select('id', 'name', 'address', 'phone', 'currency_id');
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode');
                }]);
            }]);
            $query->with(['owner' => function ($q) {
                $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company as company_name');
            }]);
        }]);
    }

    public function scopeContactRelation($q)
    {
        return $q->with(['contact' => function ($query) {
            $query->with(['company' => function ($q) {
                $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
            }]);
        }]);
    }

    public function scopePriceRelation($q)
    {
        return $q->with(['price' => function ($q) {
            $q->select('id', 'name', 'description');
        }]);
    }

    public function scopeSaletermRelation($q)
    {
        return $q->with(['saleterm' => function ($q) {
            $q->with('charge');
        }]);
    }

    public function scopeRateV2($q)
    {
        return $q->with(['rates_v2' => function ($query) {
            $query->with('origin_airport', 'destination_airport', 'airline');
            $query->with(['origin_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                $q->with('country');
            }]);
            $query->with(['currency' => function ($q) {
                $q->select('id', 'alphacode');
            }]);
            $query->with(['destination_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                $q->with('country');
            }]);
            $query->with(['charge' => function ($q) {
                $q->select('id', 'automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'amount', 'markups as markup', 'total', 'currency_id');
                $q->with('type');
                $q->with(['surcharge' => function ($q) {
                    $q->select('id', 'name', 'description', 'options');
                }]);
                $q->with(['calculation_type' => function ($q) {
                    $q->select('id', 'name', 'code', 'display_name');
                }]);
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode');
                }]);
            }]);
            $query->with(['charge_lcl_air' => function ($q) {
                $q->with('type');
                $q->with(['surcharge' => function ($q) {
                    $q->select('id', 'name', 'description', 'options');
                }]);
                $q->with(['calculation_type' => function ($q) {
                    $q->select('id', 'name', 'code', 'display_name');
                }]);
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode');
                }]);
            }]);
            $query->with(['carrier' => function ($q) {
                $q->select('id', 'name', 'uncode', 'varation as variation');
            }]);
            $query->with('inland');
            $query->with('automaticInlandLclAir');
        }]);
    }
}
