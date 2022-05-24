<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProvidersResource;
use App\Provider;
use App\Http\Traits\UtilTrait;
use App\PivotLocalChargeQuote;

class QuotationLocalChargeResource extends JsonResource
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
            $provider = Provider::where('name', $item->provider_name)->first();
            return [
                'id' => $item->id,
                'segment_id' => $this->segment_id  ?? null,
                'charge' => $item->charge,
                'charge_id' => $item->surcharge_id ?? $item->sale_term_code_id ?? null,
                'sale_code_id' => $item->sale_term_code_id ?? null,
                'charge_options' => $item->surcharge->options ?? null,
                'calculation_type' => $item->calculation_type->name ?? null,
                'calculation_type_code' => $item->calculation_type->unique_code ?? null,
                'source' => $item->source == 2 ? 'templates':'charges',
                'port' => $item->port->display_name ?? null,
                'price' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($item->price) : $item->price,
                'profit' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($item->profit) : $item->profit,
                'total' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($item->total) : $item->total,
                'units' => $item->units ?? null,
                'currency' => $item->currency->alphacode ?? null,
                'provider' => new ProvidersResource($provider)
            ];
        });

    }

    public function segmentId ($value)
    {
        $this->segment_id = $value;
        return $this;
    }

}
