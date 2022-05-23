<?php

namespace App\Http\Resources;

use App\Http\Traits\UtilTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProvidersResource;

class QuotationInlandLclResource extends JsonResource
{
    use UtilTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    protected $segment_id;

    public function toArray($request)
    {
        return $this->resource->map(function($item){
            return [
                'id' => $item->id,
                'segment_id' => $this->segment_id,
                'type' => $item->type,
                'contract' => $item->contract,
                'charge' => $item->charge,
                'distance' => $item->distance,
                'port' => $item->port->display_name ?? null,
                'address' => $item->inland_address->address ?? null,
                'provider' => new ProvidersResource($item->providers),
                'valid_from' => $item->valid_from,
                'valid_until' => $item->valid_until,
                'units' => $item->units,
                'price' => $item->total,
                'profit' => $item->profit,
                'total' => $item->total+$item->profit,
                'currency' => $item->currency->alphacode ?? null,
                'grouped_with' => $item->inland_local_group->local_charge_quote_lcl_id ?? null,
            ];
        });
    }
}
