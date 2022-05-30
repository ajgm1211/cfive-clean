<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationListResource extends JsonResource
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
            'quote_id' => $this->quote_id,
            'custom_quote_id' => $this->custom_quote_id ?? '--',
            'status' => $this->status ?? '--',
            'company_id' => $this->business_name ?? '--',
            'type' => $this->type,
            'origin' => explode("| ", $this->origin_port),
            'destiny' => explode("| ", $this->destination_port),
            'user_id' => $this->owner,
            'created_at' => date('Y-m-d H:m:s', strtotime($this->created_at)),
        ];
    }


}
