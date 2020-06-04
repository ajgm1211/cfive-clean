<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\GroupContainer;

class InlandResource extends JsonResource
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
			'reference' => $this->provider,
			'type' => $this->inland_type,
			'company_user' => $this->companyUser,
			'validity' => $this->validity,
			'expire' => $this->expire,
			'status' => $this->status,
			'gp_container' => $this->gpContainer ?? [ 'id' => 1, 'name' => 'DRY' ],
			'direction' => $this->direction,
			'restrictions' => $this->inland_company_restriction->pluck('company')
		];
	}
}
