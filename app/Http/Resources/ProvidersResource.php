<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProvidersResource extends JsonResource
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
            'name' => $this->name,
            'type' => 'provider',
            'description' => $this->description,
            'company_user_id' => $this->company_user_id,
            'referential_data' => $this->referentialData() != null ? json_decode($this->referentialData()->json_data):[]
       ];
    }
}