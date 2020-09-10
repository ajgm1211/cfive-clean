<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use App\Http\Filters\QuotationFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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

    protected $fillable = ['remarks','company_user_id', 'quote_id', 'type', 'quote_validity', 'validity_start', 'validity_end', 'origin_address', 'destination_address', 'company_id', 'contact_id', 'delivery_type', 'user_id', 'equipment', 'incoterm_id', 'status', 'date_issued', 'price_id', 'total_quantity', 'total_weight', 'total_volume', 'chargeable_weight', 'cargo_type', 'kind_of_cargo', 'commodity', 'payment_conditions', 'terms_and_conditions'];

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

    public function delivery_type()
    {
        return $this->hasOne('App\DeliveryType', 'id', 'delivery_type');
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

    public function scopeQuoteSelect($q)
    {
        return $q->select(
            'id',
            'quote_id',
            'custom_quote_id',
            'type',
            'delivery_type as delivery',
            'equipment',
            'cargo_type',
            'total_quantity',
            'total_volume',
            'total_weight',
            'chargeable_weight',
            'company_id',
            'contact_id',
            'price_id',
            'validity_start as valid_from',
            'validity_end as valid_until',
            'commodity',
            'kind_of_cargo',
            'gdp',
            'risk_level',
            'date_issued',
            'incoterm_id',
            'company_user_id',
            'status',
            'payment_conditions',
            'terms_and_conditions',
            'terms_english',
            'terms_portuguese',
            'created_at',
            'updated_at'
        );
    }

    public function scopeConditionalWhen($q, $type, $status, $integration)
    {
        return $q->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })->when($integration, function ($query, $integration) {
            return $query->whereHas('integration', function ($q) {
                $q->where('status', 0);
            });
        });
    }

    public function scopeAuthUserCompany($q, $company_user_id)
    {
        return $q->whereHas('user', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        });
    }

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
            $query->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number', 'owner', 'options');
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
                $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number', 'options');
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
            $q->select('id', 'quote_id', 'port_id', 'type');
            $q->with(['port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name');
            }]);
            $q->with(['charge' => function ($q) {
                $q->select('id', 'sale_term_id', 'charge', 'detail', 'c20 as c20DV', 'c40 as c40DV', 'c40hc as c40HC', 'c40nor as c40NOR', 'c45 as c45HC', 'units', 'amount', 'rate', 'markup', 'currency_id');
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'alphacode');
                }]);
            }]);
        }]);
    }

    public function scopeRateV2($q)
    {
        return $q->with(['rates_v2' => function ($query) {
            $query->select(
                'id',
                'quote_id',
                'contract',
                'validity_start as valid_from',
                'validity_end as valid_until',
                'origin_port_id',
                'destination_port_id',
                'origin_port_id',
                'destination_port_id',
                'carrier_id',
                'airline_id',
                'currency_id',
                'remarks',
                'remarks_english',
                'remarks_spanish',
                'remarks_portuguese',
                'schedule_type',
                'transit_time',
                'via'
            );
            $query->with('origin_airport', 'destination_airport');
            $query->with(['carrier' => function ($q) {
                $q->select('id', 'name', 'uncode', 'image as url');
            }]);
            $query->with(['airline' => function ($q) {
                $q->select('id', 'name');
            }]);
            $query->with(['origin_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id');
                $q->with(['country' => function ($q) {
                    $q->select('id', 'code', 'name', 'continent');
                }]);
            }]);
            $query->with(['currency' => function ($q) {
                $q->select('id', 'alphacode');
            }]);
            $query->with(['destination_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id');
                $q->with(['country' => function ($q) {
                    $q->select('id', 'code', 'name', 'continent');
                }]);
            }]);
            $query->with(['charge' => function ($q) {
                $q->select('id', 'automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'amount as price', 'markups as markup', 'total as total_price', 'currency_id');
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
            $query->with(['inland' => function ($q) {
                $q->select('id', 'quote_id', 'automatic_rate_id', 'provider', 'contract', 'port_id', 'type', 'distance', 'rate as price', 'markup', 'currency_id', 'validity_start as valid_from', 'validity_start as valid_until');
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'name', 'alphacode');
                }]);
                $q->with(['port' => function ($q) {
                    $q->select('id', 'name', 'code', 'display_name');
                }]);
            }]);
            $query->with(['inland_lcl' => function ($q) {
                $q->select('id', 'quote_id', 'automatic_rate_id', 'provider', 'contract', 'port_id', 'type', 'distance', 'units', 'price_per_unit', 'markup', 'currency_id', 'validity_start as valid_from', 'validity_start as valid_until');
                $q->with(['currency' => function ($q) {
                    $q->select('id', 'name', 'alphacode');
                }]);
                $q->with(['port' => function ($q) {
                    $q->select('id', 'name', 'code', 'display_name');
                }]);
            }]);
        }]);
    }

    public function scopeAutomaticRate($q)
    {
        return $q->with(['rates_v2' => function ($query) {
            $query->select(
                'id',
                'quote_id',
                'contract',
                'validity_start as valid_from',
                'validity_end as valid_until',
                'origin_port_id',
                'destination_port_id',
                'origin_port_id',
                'destination_port_id',
                'carrier_id',
                'airline_id',
                'currency_id',
                'remarks',
                'remarks_english',
                'remarks_spanish',
                'remarks_portuguese',
                'schedule_type',
                'transit_time',
                'via'
            );
            $query->with('origin_airport', 'destination_airport');
            $query->with(['carrier' => function ($q) {
                $q->select('id', 'name', 'uncode', 'image as url');
            }]);
            $query->with(['airline' => function ($q) {
                $q->select('id', 'name');
            }]);
            $query->with(['origin_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id');
                $q->with(['country' => function ($q) {
                    $q->select('id', 'code', 'name', 'continent');
                }]);
            }]);
            $query->with(['currency' => function ($q) {
                $q->select('id', 'alphacode');
            }]);
            $query->with(['destination_port' => function ($q) {
                $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id');
                $q->with(['country' => function ($q) {
                    $q->select('id', 'code', 'name', 'continent');
                }]);
            }]);
            $query->with(['charge' => function ($q) {
                $q->select('id', 'automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'amount', 'markups', 'total', 'currency_id');
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
            $query->with('inland');
            $query->with('automaticInlandLclAir');
        }]);
    }

    public function automatic_inland_lcl_airs()
    {
        $this->hasMany('App\AutomaticInlandLclAir');
    }

    public function automatic_inlands()
    {
        $this->hasMany('App\AutomaticInland');
    }  
    
    public function integration_quote_statuses()
    {
        $this->hasMany('App\IntegrationQuoteStatus');
    }
    
    public function package_load_v2s()
    {
        $this->hasMany('App\PackageLoadV2');
    }  

    public function payment_conditions()
    {
        $this->hasMany('App\PaymentCondition');
    }
    
    public function sale_term_v2s()
    {
        $this->hasMany('App\SaleTermV2');
    }

    public function duplicate()
    {

        $new_quote = $this->replicate();
        $new_quote->quote_id .= ' copy';
        $new_quote->save();

        $this->with('automatic_inland_lcl_airs','automatic_inlands','integration_quote_statuses',
                    'package_load_v2s','rate_v2','pdf_option','payment_conditions');
       
        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                $newRelationship = $relationRecord->replicate();
                $newRelationship->quote_id = $new_quote->id;
                $newRelationship->save();
                }
            }
        
        return $new_quote;
    }

    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new QuotationFilter($request, $builder))->filter();
    }

    public function getContainerCodes($equip,$getGroup=false)
    {   
        $size = count($equip);
        if($size != 0){
            $equip_array = explode(",",str_replace(["\"","[","]"],"",$equip));
            $full_equip = "";
        
        foreach ($equip_array as $eq){
            $full_equip.=Container::where('id','=',$eq)->first()->code.",";
            if($getGroup){
                $group_id = Container::where('id','=',$eq)->first()->gp_container_id;
                $group = GroupContainer::where('id','=',$group_id)->first();

                return $group;
            }
        }

        return $full_equip;
        } else {
            return $equip;
        }
        
    }

    public function getContainerArray($equip)
    {
        $cont_ids=[];
        $cont_array = explode(",",$equip);
        foreach ($cont_array as $cont){
            if ($cont != ""){
                $wh = Container::where('code','=',$cont)->first()->id;
                array_push($cont_ids,$wh);
            }
        }
        $conts = "[\"".implode("\",\"",$cont_ids)."\"]";

        return $conts;
    }

    public function originDest($reqPorts)
    {
        foreach($reqPorts as $port){
            $info = explode("-",$port);
            $ports[] = $info[0];
        }

        return $ports;

    }

}
