<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchApiResource extends JsonResource
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
            'equipment' => $this->equipment,
            'pick_up_date' => $this->pick_up_date,
            'delivery' => $this->delivery,
            'type' => $this->type,
            'direction' => $this->direction,
            'company_user_id' => $this->company_user_id,
            'user_id' => $this->user_id,
            'contact_id' => $this->contact_id,
            'company_id' => $this->company_id,
        ];
    }
}
