<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceLevelResource extends JsonResource
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
            'display_name' => $this->display_name,
            'description' => $this->description,
            'type' => $this->type,
            'options' => $this->options,
            'company_user' => $this->company_user,
            'company_restrictions' => $this->companies,
            'group_restrictions' => $this->company_groups,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
