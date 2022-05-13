<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
use App\Http\Resources\ProvidersResource;
use App\Provider;

class QuotationInlandResource extends JsonResource
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
            $provider = $item->providers ?? Provider::where('name', $item->provider)->first();

            return [
                'id' => $item->id,
                'segment_id' => $this->segment_id,
                'type' => $item->type,
                'contract' => $item->contract,
                'charge' => $item->charge,
                'distance' => $item->distance,
                'port' => $item->port->display_name ?? null,
                'address' => $item->inland_address->address ?? null,
                'provider' => new ProvidersResource($provider),
                'valid_from' => $item->valid_from,
                'valid_until' => $item->valid_until,
                'price' => $this->arrayToFloat($item->price),
                'profit' => $this->arrayToFloat($item->profit),
                'total' => $this->setTotal($item->price, $item->profit),
                'currency' => $item->currency->alphacode ?? null,
                'grouped_with' => $item->inland_local_group->local_charge_quote_id ?? null,
            ];
        });
    }

    public function segmentId ($value)
    {
        $this->segment_id = $value;
        return $this;
    }
}
