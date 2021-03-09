<?php

namespace App;

use App\Http\Filters\QuotationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class QuoteV2 extends Model implements HasMedia
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
        'pdf_options' => 'json',
    ];

    protected $attributes = [
        'language_id' => 1,
    ];

    protected $fillable = [
        'remarks', 'company_user_id', 'quote_id', 'type', 'quote_validity', 'validity_start', 'validity_end',
        'origin_address', 'destination_address', 'company_id', 'contact_id', 'delivery_type', 'user_id', 'equipment', 'incoterm_id',
        'status', 'date_issued', 'price_id', 'total_quantity', 'total_weight', 'total_volume', 'chargeable_weight', 'cargo_type',
        'kind_of_cargo', 'commodity', 'payment_conditions', 'terms_and_conditions', 'terms_english', 'terms_portuguese', 'remarks_english',
        'remarks_spanish', 'remarks_portuguese', 'language_id', 'pdf_options', 'localcharge_remarks', 'custom_quote_id', 'custom_incoterm', 'cargo_type_id',
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
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

    public function inland()
    {
        return $this->hasMany('App\AutomaticInland', 'quote_id', 'id');
    }

    public function inland_lcl()
    {
        return $this->hasMany('App\AutomaticInlandLclAir', 'quote_id', 'id');
    }

    public function charge()
    {
        return $this->hasManyThrough('App\Charge', 'App\AutomaticRate', 'quote_id', 'automatic_rate_id');
    }

    public function origin_harbor()
    {
        return $this->hasManyThrough('App\Harbor', 'App\AutomaticRate', 'quote_id', 'id', 'id', 'origin_port_id');
    }

    public function destination_harbor()
    {
        return $this->hasManyThrough('App\Harbor', 'App\AutomaticRate', 'quote_id', 'id', 'id', 'destination_port_id');
    }

    public function carrier()
    {
        return $this->hasManyThrough('App\Carrier', 'App\AutomaticRate', 'quote_id', 'id', 'id', 'carrier_id');
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

    public function kind_of_cargo()
    {
        return $this->hasOne('App\CargoKind', 'name', 'kind_of_cargo');
    }

    public function status_quote()
    {
        return $this->hasOne('App\StatusQuote', 'name', 'status');
    }

    public function scopeExclude($query, $value = array())
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }

    public function language()
    {
        return $this->hasOne('App\Language', 'id', 'language_id');
    }

    public function cargoType()
    {
        return $this->hasOne('App\CargoType', 'id', 'cargo_type_id');
    }

    public function getRate($type, $port, $carrier)
    {

        $rate = null;

        if ($type == 1) {
            $rate = $this->rates_v2()->where(['quote_id' => $this->id, 'origin_port_id' => $port, 'carrier_id' => $carrier])->first();
        } else if ($type == 2) {
            $rate = $this->rates_v2()->where(['quote_id' => $this->id, 'destination_port_id' => $port, 'carrier_id' => $carrier])->first();
        }

        if ($rate == null) {
            $rate = $this->rates_v2()->where('quote_id', $this->id)->where(function ($query) use ($port) {
                $query->where('origin_port_id', $port)->orWhere('destination_port_id', $port);
            })->first();
        }

        return $rate;
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

    public function scopeNewQuoteSelect($q)
    {
        return $q->select(
            'id',
            'type',
            'quote_id',
            'custom_quote_id',
            'equipment',
            'delivery_type as delivery',
            'cargo_type',
            'incoterm_id',
            'commodity',
            'kind_of_cargo',
            'gdp',
            'status',
            'risk_level',
            'date_issued',
            'remarks_spanish',
            'remarks_english',
            'remarks_portuguese',
            'localcharge_remarks',
            'terms_and_conditions as terms_spanish',
            'terms_english',
            'terms_portuguese',
            'payment_conditions',
            'contact_id',
            'company_id',
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

    public function scopeFilterByType($q)
    {
        return $q->where('type', 'FCL')->orWhere('type', 'LCL');
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

    public function scopeNewUserRelation($q)
    {
        return $q->with(['user' => function ($query) {
            $query->select('id', 'name', 'lastname', 'email', 'phone');
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

    public function scopeNewCompanyRelation($q)
    {
        return $q->with(['company' => function ($query) {
            $query->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number', 'options');
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

    public function scopeNewContactRelation($q)
    {
        return $q->with(['contact' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'phone', 'options');
        }]);
    }

    public function scopeIncotermRelation($q)
    {
        return $q->with(['incoterm' => function ($q) {
            $q->select('id', 'name');
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

    public function scopeOriginHarborRelation($q)
    {
        return $q->with(['origin_harbor' => function ($q) {
            $q->select('id', 'display_name');
        }]);
    }

    public function scopeDestinationHarborRelation($q)
    {
        return $q->with(['destination_harbor' => function ($q) {
            $q->select('id', 'display_name');
        }]);
    }

    public function scopeNewRateV2($q)
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
                'carrier_id',
                'currency_id',
                'remarks',
                'remarks_english',
                'remarks_spanish',
                'remarks_portuguese',
                'transit_time',
                'via'
            );
            $query->with(['origin_port' => function ($q) {
                $q->select('id', 'name', 'code');
            }]);
            $query->with(['destination_port' => function ($q) {
                $q->select('id', 'name', 'code');
            }]);
            $query->with(['carrier' => function ($q) {
                $q->select('id', 'name', 'uncode', 'image as url');
            }]);
            $query->with(['currency' => function ($q) {
                $q->select('id', 'alphacode');
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

    public function inland_addresses()
    {
        return $this->hasMany('App\InlandAddress', 'quote_id', 'id');
    }

    public function automatic_inland_totals()
    {
        return $this->hasMany('App\AutomaticInlandTotal', 'quote_id', 'id');
    }

    public function automatic_inland_address()
    {
        return $this->hasMany('App\InlandAddress', 'quote_id', 'id');
    }

    public function automatic_rate_totals()
    {
        return $this->hasMany('App\AutomaticRateTotal', 'quote_id', 'id');
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

    public function local_charges()
    {
        return $this->hasMany('App\LocalChargeQuote', 'quote_id', 'id');
    }

    public function local_charges_totals()
    {
        return $this->hasMany('App\LocalChargeQuoteTotal', 'quote_id', 'id');
    }

    public function local_charges_lcl()
    {
        return $this->hasMany('App\LocalChargeQuoteLcl', 'quote_id', 'id');
    }

    public function local_charges_lcl_totals()
    {
        return $this->hasMany('App\LocalChargeQuoteLclTotal', 'quote_id', 'id');
    }

    public function duplicate()
    {
        $company_user = Auth::user('web')->worksAt();
        $company_code = strtoupper(substr($company_user->name, 0, 2));
        $higherq_id = $company_user->getHigherId($company_code);
        $newq_id = $company_code . '-' . strval($higherq_id + 1);

        $new_quote = $this->replicate();
        $new_quote->quote_id = $newq_id;
        $new_quote->save();

        if ($new_quote->type == 'FCL') {
            $this->load(
                'rates_v2',
                'inland_addresses',
                'local_charges',
                'local_charges_totals',
                'pdf_option'
            );
        } else if ($new_quote->type == 'LCL') {
            $this->load(
                'rates_v2',
                'inland_addresses',
                'local_charges_lcl',
                'local_charges_lcl_totals'
            );
        }

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            if (!is_a($relation, 'Illuminate\Database\Eloquent\Collection')) {
                $relation->duplicate($new_quote);
            } else {
                foreach ($relation as $relationRecord) {
                    $newRelationship = $relationRecord->duplicate($new_quote);
                }
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

    public function scopeTypeFCL($query)
    {
        return $query->where('type', '=', 'FCL');
    }

    public function getContainerCodes($equip, $getGroup = false)
    {

        $size = count((array) $equip);

        if ($size != 0 && $equip != "[]") {
            $equip_array = explode(",", str_replace(["\"", "[", "]"], "", $equip));
            $equip_array = $this->validateEquipment($equip_array);
            $full_equip = "";

            foreach ($equip_array as $eq) {
                $full_equip .= Container::where('id', '=', $eq)->first()->code . ",";
                if ($getGroup) {
                    $group_id = Container::where('id', '=', $eq)->first()->gp_container_id;
                    $group = GroupContainer::where('id', '=', $group_id)->first();

                    return $group;
                }
            }

            return $full_equip;
        } else {
            return $equip;
        }
    }

    public function getContainerArray($equip, $type='id')
    {
        if ($equip != '[]') {
            $cont_ids = [];
            $cont_array = explode(",", $equip);
            foreach ($cont_array as $cont) {
                if ($cont != "") {
                    if($type == 'id'){
                        $wh = Container::where('code', '=', $cont)->first()->id;
                        array_push($cont_ids, $wh);
                    }
                }
            }
            $conts = "[\"" . implode("\",\"", $cont_ids) . "\"]";

            return $conts;
        } else {
            return $equip;
        }
    }

    public function originDest($reqPorts)
    {
        foreach ($reqPorts as $port) {
            $info = explode("-", $port);
            $ports[] = $info[0];
        }

        return $ports;
    }

    public function getDeliveryAttribute($value)
    {

        if ($value == 1) {
            $value = 'Port to Port';
        } elseif ($value == 2) {
            $value = 'Port to Door';
        } elseif ($value == 3) {
            $value = 'Door to Port';
        } elseif ($value == 4) {
            $value = 'Door to Door';
        } else {
            $value = 'Port to Port';
        }

        return $value;
    }

    public function validateEquipment(array $equipment)
    {

        foreach ($equipment as $index => $eq) {
            if ($eq == "20") {
                $equipment[$index] = "1";
            } else if ($eq == "40") {
                $equipment[$index] = "2";
            } else if ($eq == "40HC") {
                $equipment[$index] = "3";
            } else if ($eq == "45") {
                $equipment[$index] = "4";
            }
            if ($eq == "40NOR") {
                $equipment[$index] = "5";
            } else if ($eq == "20RF") {
                $equipment[$index] = "6";
            } else if ($eq == "40RF") {
                $equipment[$index] = "7";
            } else if ($eq == "40HCRF") {
                $equipment[$index] = "8";
            } else if ($eq == "20OT") {
                $equipment[$index] = "9";
            } else if ($eq == "40OT") {
                $equipment[$index] = "10";
            } else if ($eq == "20FR") {
                $equipment[$index] = "11";
            } else if ($eq == "40FR") {
                $equipment[$index] = "12";
            }
        }
        return $equipment;
    }

    public function exchangeRates()
    {
        $exchange = [];
        $included = [];
        $client = $this->company_user->first();

        if(isset($this->pdf_options['exchangeRates']) && $this->pdf_options['exchangeRates'] != null){
            $options = $this->pdf_options['exchangeRates'];

            foreach($options as $opt){
                if($opt['custom']){
                    array_push($exchange, $opt);
                    array_push($included, $opt['alphacode']);
                }
            }
        }

        $rateTotals = $this->automatic_rate_totals()->get();
        $inlandTotals = $this->automatic_inland_totals()->get();
        
        if($this->type == 'FCL'){
            $localchargeTotals = $this->local_charges_totals()->get();
        }else if($this->type == 'LCL'){
            $localchargeTotals = $this->local_charges_lcl_totals()->get();
        }

        $allTotals = $rateTotals->concat($inlandTotals)->concat($localchargeTotals);

        foreach($allTotals as $total){
            $currency = Currency::where('id', $total->currency_id)->first();
            
            if(!in_array($currency->alphacode,$included)){
                $currencyExchange = [ 
                    'alphacode' => $currency->alphacode, 
                    'exchangeUSD' => $currency->rates, 
                    'exchangeEUR' => $currency->rates_eur,
                    'custom' => false
                ];

                array_push($exchange, $currencyExchange);
                array_push($included, $currency->alphacode);
            }

        }

        return $exchange;
    }

    public function updatePdfOptions($option = null)
    {
        if($this->pdf_options==null || count($this->pdf_options) != 5){            
            $client = $this->company_user()->first();
            $client_currency = Currency::find($client->currency_id);

            $exchangeRates = $this->exchangeRates();
    
            $pdfOptions = [
                "allIn" =>true, 
                "showCarrier"=>true, 
                "showTotals"=>false, 
                "totalsCurrency" =>$client_currency,
                "exchangeRates" => $exchangeRates
            ];
            
            $this->pdf_options = $pdfOptions;
            $this->save();
        }

        if($option == 'exchangeRates'){
            $pdfOptions = $this->pdf_options;

            $exchangeRates = $this->exchangeRates();

            $pdfOptions['exchangeRates'] = $exchangeRates;

            $this->pdf_options = $pdfOptions;
            $this->save();
        }
    }

    public function getContainersFromEquipment($equipment, $type = 'model')
    {
        if (isset($equipment) && count($equipment) != 0 && $equipment != "[]") {
            $equip_array = explode(",", str_replace(["\"", "[", "]"], "", $equipment));
            $equip_array = $this->validateEquipment($equip_array);
            $containers = [];

            foreach($equip_array as $container_id){
                $cont = Container::where('id',$container_id)->first();

                if($type == 'model'){
                    array_push($containers, $cont);
                }else if($type == 'array'){
                    array_push($containers, $cont->toArray());
                }
            }

            return $containers;

        }else{

            return $equipment;

        }
    }
}
