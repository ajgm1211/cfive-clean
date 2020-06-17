<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Charge extends Model
{

    protected $casts = [
        'amount' => 'array',
        'markups' => 'array',
        'total' => 'array',
    ];

    protected $fillable = ['automatic_rate_id', 'type_id', 'surcharge_id', 'calculation_type_id', 'amount', 'markups', 'currency_id', 'total'];

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

        $value = array();

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
                    if ($k == 'c' . $container->code) {
                        $value['c' . $container->code] = $amount_value;
                    }
                }
            }
        }

        return $value;
    }

    public function getMarkupAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = array();

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

        return $value;
    }

    public function getTotalPriceAttribute($array)
    {
        $array = json_decode(json_decode($array));

        $value = array();

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
                        if ($k == 'c' . $container->code) {
                            $value['c' . $container->code] = $amount_value;
                        }
                    }
                }
            }
        }

        return $value;
    }
}
