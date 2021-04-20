<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationInlandResource extends JsonResource
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
            'type' => $this->type,
            'contract' => $this->contract,
            'distance' => $this->distance,
            'port' => $this->port->display_name ?? null,
            'address' => $this->inland_address->address ?? null,
            'provider' => $this->providers->name ?? null,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'price' => $this->arrayToFloat($this->price),
            'profit' => $this->arrayToFloat($this->profit),
            'total' => $this->setTotal($this->price, $this->profit),
            'currency' => $this->currency->alphacode ?? null,
        ];
    }

    public function arrayToFloat($array)
    {

        $new_array = [];

        foreach ($array as $key => $item) {
            $new_array[$key] = (float) $item;
        }

        return $new_array;
    }

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
