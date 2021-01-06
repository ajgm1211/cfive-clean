<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InlandAddressResource extends JsonResource
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
            'address' => $this->address,
            'quote_id' => $this->quote_id,
            'port_id' => $this->port_id,
        ];

        return $data;
    }
}
