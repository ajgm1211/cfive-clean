<?php

namespace App\Http\Traits;

use App\Container;
use App\Currency;
use Illuminate\Support\Collection as Collection;

trait UtilTrait
{

    public function transformEquipment($quotes)
    {
        $containers = Container::select('id', 'code')->get();

        foreach ($quotes as $quote) {
            $array = array();
            foreach (json_decode($quote->equipment) as $val) {
                if ($val == '20') {
                    $val = 1;
                } elseif ($val == '40') {
                    $val = 2;
                } elseif ($val == '40HC') {
                    $val = 3;
                } elseif ($val == '45HC') {
                    $val = 4;
                } elseif ($val == '40NOR') {
                    $val = 5;
                }

                foreach ($containers as $cont) {
                    if ($val == $cont->id) {
                        array_push($array, $cont->code);
                        $quote->equipment = $array;
                    }
                }
            }
        }
    }

    public function transformEquipmentSingle($quote)
    {
        $containers = Container::select('id', 'code')->get();

        $array = array();
        foreach (json_decode($quote->equipment) as $val) {
            if ($val == '20') {
                $val = 1;
            } elseif ($val == '40') {
                $val = 2;
            } elseif ($val == '40HC') {
                $val = 3;
            } elseif ($val == '45HC') {
                $val = 4;
            } elseif ($val == '40NOR') {
                $val = 5;
            }

            foreach ($containers as $cont) {
                if ($val == $cont->id) {
                    array_push($array, $cont->code);
                    $quote->equipment = $array;
                }
            }
        }
    }

    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }
}
