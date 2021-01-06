<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Http\Filters\ChargeFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Charge extends Model
{
    protected $casts = [
        'amount' => 'array',
        'markups' => 'array',
        'total' => 'array',
    ];

    protected $appends = ['currency_code'];

    protected $fillable = ['automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'amount', 'markups', 'currency_id', 'total', 'provider_name'];

    public function automatic_rate()
    {
        return $this->belongsTo('App\AutomaticRate', 'automatic_rate_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function type()
    {
        return $this->belongsTo('App\TypeDestiny');
    }

    public function surcharge()
    {
        return $this->hasOne('App\Surcharge', 'id', 'surcharge_id');
    }

    public function calculation_type()
    {
        return $this->hasOne('App\CalculationType', 'id', 'calculation_type_id');
    }

    public function scopeCalculationType($query)
    {
        return $query->with(['calculation_type' => function ($q) {
            $q->select('id', 'name', 'display_name', 'code');
        }]);
    }

    public function getPriceAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = [];

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount_value) {
                if ($k == 'c20') {
                    $value['c20DV'] = $amount_value;
                } elseif ($k == 'c40') {
                    $value['c40DV'] = $amount_value;
                } elseif ($k == 'c40hc') {
                    $value['c40HC'] = $amount_value;
                } elseif ($k == 'c40nor') {
                    $value['c40NOR'] = $amount_value;
                } elseif ($k == 'c45hc') {
                    $value['c45HC'] = $amount_value;
                } else {
                    $containers = Container::all();
                    foreach ($containers as $container) {
                        if ($k == 'c'.$container->code) {
                            $value['c'.$container->code] = $amount_value;
                        }
                    }
                }
            }
        }

        return $value;
    }

    public function getMarkupAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = [];

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount_value) {
                if ($k == 'm20') {
                    $value['m20DV'] = $amount_value;
                } elseif ($k == 'm40') {
                    $value['m40DV'] = $amount_value;
                } elseif ($k == 'm40hc') {
                    $value['m40HC'] = $amount_value;
                } elseif ($k == 'm40nor') {
                    $value['m40NOR'] = $amount_value;
                } elseif ($k == 'm45hc') {
                    $value['m45HC'] = $amount_value;
                } else {
                    $containers = Container::all();
                    foreach ($containers as $container) {
                        if ($k == 'm'.$container->code) {
                            $value['m'.$container->code] = $amount_value;
                        }
                    }
                }
            }
        }

        return $value;
    }

    public function getProfitAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = array();

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount_value) {
                if ($k == 'm20') {
                    $value['m20DV'] = $amount_value;
                } elseif ($k == 'm40') {
                    $value['m40DV'] = $amount_value;
                } elseif ($k == 'm40hc') {
                    $value['m40HC'] = $amount_value;
                } elseif ($k == 'm40nor') {
                    $value['m40NOR'] = $amount_value;
                } elseif ($k == 'm45hc') {
                    $value['m45HC'] = $amount_value;
                } else {
                    $containers = Container::all();
                    foreach ($containers as $container) {
                        if ($k == 'm' . $container->code) {
                            $value['m' . $container->code] = $amount_value;
                        }
                    }
                }
            }
        }

        return $value;
    }

    public function getTotalPriceAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = [];

        if ($array != null || $array != '') {
            foreach ($array as $k => $amount_value) {
                if ($k == 'c20') {
                    $value['c20DV'] = $amount_value;
                } elseif ($k == 'c40') {
                    $value['c40DV'] = $amount_value;
                } elseif ($k == 'c40hc') {
                    $value['c40HC'] = $amount_value;
                } elseif ($k == 'c40nor') {
                    $value['c40NOR'] = $amount_value;
                } elseif ($k == 'c45hc') {
                    $value['c45HC'] = $amount_value;
                } else {
                    $containers = Container::all();
                    foreach ($containers as $container) {
                        if ($k == 'c'.$container->code) {
                            $value['c'.$container->code] = $amount_value;
                        }
                    }
                }
            }
        }

        return $value;
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new ChargeFilter($request, $builder))->filter();
    }

    public function scopeFilterByAutorate($query, $automatic_rate_id)
    {
        return $query->where('automatic_rate_id', '=', $automatic_rate_id);
    }

    public function setContractInfo($info_decoded, $rate_decoded, $autoRate)
    {

        $rates = json_encode($rate_decoded->rate);
        $markups = json_encode($rate_decoded->markups);
        $remarks = $info_decoded->remarks;
        $transit_time = $info_decoded->transit_time;
        $via = $info_decoded->via;

        $this->amount = $rates;
        $this->markups = $markups;
        $this->currency_id = $info_decoded->currency->id;
        $this->total = $rates;
        $this->save();

        $autoRate->contract = $info_decoded->contract->name;
        $autoRate->origin_port_id = $info_decoded->port_origin->id;
        $autoRate->destination_port_id = $info_decoded->port_destiny->id;
        $autoRate->carrier_id = $info_decoded->carrier->id;
        $autoRate->currency_id = $info_decoded->currency->id;
        $autoRate->remarks = $remarks;
        if ($transit_time != '') {
            $autoRate->transit_time = $transit_time;
        }
        if ($via != '') {
            $autoRate->via = $via;
        }
        $autoRate->save();
    }

    public function setCalculationType($containerType)
    {
        $calctype = '';
        if ($containerType == '1') {
            $calctype = '3';
        } else if ($containerType == '2') {
            $calctype = '19';
        } else if ($containerType == '3') {
            $calctype = '20';
        } else if ($containerType == '4') {
            $calctype = '21';
        }

        $this->calculation_type_id = $calctype;
        $this->save();
    }

    public function getCurrencyCodeAttribute()
    {
        return $this->currency()->first()->alphacode ?? null;
    }
}
