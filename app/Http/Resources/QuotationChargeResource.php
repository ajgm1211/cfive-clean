<?php

namespace App\Http\Resources;

use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type->description,
            'charge' => $this->surcharge->name ?? 'Ocean Freight',
            'calculation_type' => $this->calculation_type->name ?? null,
            'port' => $this->getType($this->type_id),
            'price' => $this->arrayToFloat($this->price) ?? [],
            'profit' => $this->arrayToFloat($this->profit) ?? [],
            'total' => $this->setTotal($this->price, $this->profit),
            'units' => $this->units ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => $this->automatic_rate->carrier->name ?? null,
        ];
    }
    
    /**
     * getType
     *
     * @param  mixed $type
     * @return void
     */
    public function getType($type)
    {
        if ($type == 1) {
            return $this->automatic_rate->origin_port->display_name;
        } elseif ($type == 2) {
            return $this->automatic_rate->destination_port->display_name;
        } else {
            return null;
        }
    }

    /**
     * arrayToFloat
     *
     * @param  mixed $array
     * @return void
     */
    public function arrayToFloat($array)
    {

        $new_array = [];

        foreach ((array)$array as $key => $item) {
            $new_array[$key] = (float) $item;
        }

        return $new_array;
    }
    
    /**
     * setTotal
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

        return $new_array;
    }
}
