<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomaticInlandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'quote_id' => $this->quote_id,
            'automatic_rate_id' => $this->rate()->first(),
            'port_id' => $this->port,
            'provider_id' => $this->providers()->first(),
            'provider' => $this->provider,
            'contract' => $this->contract,
            'type' => $this->type,
            'charge' => $this->charge,
            'validity_start' => $this->validity_start,
            'validity_end' => $this->validity_end,
            'distance' => $this->distance,
            'currency_id' => $this->currency,
            'rate' => $this->rate,
            'markup' => $this->markup,
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data)
    {

        if ($this->rate != null) {
            if (!is_array($data['rate'])) {
                $totals = json_decode($data['rate']);
            }else{
                $totals = $data['rate'];
            }
            foreach ($totals as $code => $total) {
                $total_key = str_replace('c', '', $code);
                $data['rates_' . $total_key] = isDecimal($total,true);
            }
        }

        return $data;
    }
}
