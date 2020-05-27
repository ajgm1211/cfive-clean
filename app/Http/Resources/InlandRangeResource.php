<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection as Collection;
class InlandRangeResource extends JsonResource
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
            'lower' => $this->lower,
            'upper' => $this->upper,
            'details' => json_decode($this->details),
            'inland' => $this->inland,
            'currency' => $this->currency,
            'gp_container' => $this->gpContainer,
             
          ];
    }
}
