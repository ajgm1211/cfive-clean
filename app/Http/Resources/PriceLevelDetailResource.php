<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceLevelDetailResource extends JsonResource
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
            'amount' => $this->amount,
            'price_level' => $this->price_level,
            'currency' => $this->currency,
            'price_level_apply' => $this->price_level_apply,
            'direction' => $this->direction,
        ];
    }
}
