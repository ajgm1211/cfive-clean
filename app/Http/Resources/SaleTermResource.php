<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleTermResource extends JsonResource
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
			'type_id' => $this->type_id,
			'group_container_id' => $this->group_container_id,
			'port_id' => $this->port_id,
			'company_user_id' => $this->company_user_id,
			'company_user' => $this->companyUser,
			'type' => $this->type,
			'group_container' => $this->group_container  ?? [ 'id' => 1, 'name' => 'DRY' ],
			'port' => $this->port,
		];
    }
}
