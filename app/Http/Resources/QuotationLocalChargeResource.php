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
    public function toArray($request)
    {
        $provider = Provider::where('name', $this->provider_name)->first();
        
        return [
            'id' => $this->id,
            'charge' => $this->charge,
            'charge_id' => $this->surcharge_id ?? $this->sale_term_code_id ?? null,
            'charge_options' => $this->surcharge->options ?? null,
            'calculation_type' => $this->calculation_type->name ?? null,
            'calculation_type_code' => $this->calculation_type->unique_code ?? null,
            'source' => $this->source == 2 ? 'templates':'surcharges',
            'port' => $this->port->display_name ?? null,
            'price' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->price) : $this->price,
            'profit' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->profit) : $this->profit,
            'total' => is_a($this->resource, "App\LocalChargeQuote") ? $this->arrayToFloat($this->total) : $this->total,
            'units' => $this->units ?? null,
            'currency' => $this->currency->alphacode ?? null,
            'provider' => new ProvidersResource($provider)
        ];

    }
}
