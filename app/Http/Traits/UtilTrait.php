<?php

namespace App\Http\Traits;

use App\Container;
use App\Currency;
use Illuminate\Support\Collection as Collection;

trait UtilTrait
{    
    /**
     * transformEquipment
     *
     * @param  mixed $quotes
     * @return void
     */
    public function transformEquipment($quotes)
    {
        $containers = Container::select('id', 'code')->get();

        foreach ($quotes as $quote) {
            $array = [];
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
    
    /**
     * transformEquipmentSingle
     *
     * @param  mixed $quote
     * @return void
     */
    public function transformEquipmentSingle($quote)
    {
        $containers = Container::select('id', 'code')->get();

        $array = [];
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

    
    /**
     * processOldContainers
     *
     * @param  mixed $array
     * @param  mixed $type
     * @return void
     */
    public function processOldContainers($array, $type)
    {
        if (!empty($array)) {
            switch ($type) {
                case 'amounts':
                    foreach ($array as $k => $amount_value) {
                        if ($k == 'c20') {
                            $array['c20DV'] = $amount_value;
                            unset($array['c20']);
                        } elseif ($k == 'c40') {
                            $array['c40DV'] = $amount_value;
                            unset($array['c40']);
                        } elseif ($k == 'c40hc') {
                            $array['c40HC'] = $amount_value;
                            unset($array['c40hc']);
                        } elseif ($k == 'c40nor') {
                            $array['c40NOR'] = $amount_value;
                            unset($array['c40nor']);
                        } elseif ($k == 'c45hc') {
                            $array['c45HC'] = $amount_value;
                            unset($array['c45hc']);
                        }
                    }
                    return $array;
                    break;
                case 'markups':
                    foreach ($array as $k => $markup_value) {
                        if ($k == 'm20') {
                            $array['m20DV'] = $markup_value;
                            unset($array['m20']);
                        } elseif ($k == 'm40') {
                            $array['m40DV'] = $markup_value;
                            unset($array['m40']);
                        } elseif ($k == 'm40hc') {
                            $array['m40HC'] = $markup_value;
                            unset($array['m40hc']);
                        } elseif ($k == 'm40nor') {
                            $array['m40NOR'] = $markup_value;
                            unset($array['m40nor']);
                        } elseif ($k == 'm45hc') {
                            $array['m45HC'] = $markup_value;
                            unset($array['m45hc']);
                        }
                    }
                    return $array;
                    break;
            }
        }
    }
    
    /**
     * ratesCurrency
     *
     * @param  mixed $id
     * @param  mixed $typeCurrency
     * @return void
     */
    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == 'USD') {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }

        return $rateC;
    }

    /**
     * Convert values in array to float using array_map
     *
     * @param  mixed $array
     * @return void
     */
    public function arrayMapToFloat($array)
    {
        return array_map('floatval', $array);
    }
    
    /**
     * Convert values in array to float using foreach
     *
     * @param  mixed $array
     * @return void
     */
    public function arrayToFloat($array){
        $new_array = [];

        foreach((array)$array as $key=>$item){
            $new_array[$key] = (float) $item;
        }

        return $new_array;
    }

    /**
     * set total adding price and profit
     *
     * @param  mixed $price
     * @param  mixed $profit
     * @return void
     */
    public function setTotal($price, $profit)
    {

        $new_array = [];
        
        foreach ($price as $key => $item) {
            foreach ($profit as $k => $value) {
                $str1 = ltrim($key, 'c');
                $str2 = ltrim($k, 'm');
                $total = 0;
                if($str1 == $str2){
                    $total = (float) $item + (float) $value;
                    $new_array[$key] = (float) $total;
                }
            }
        }
        $merge = array_merge($price, $new_array);
        $floats = $this->arrayMapToFloat($merge);
        return $floats;
    }
}
