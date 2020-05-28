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
		/* Example */
		return [
			'id' => 1,
			'reference' => 'Provider 3',
			'status' => 'publish',
			'type' => [ 'id' => 1, 'name' => "Per KM", 'created_at' => null, 'updated_at' => null ],
			'gp_container' => GroupContainer::find(rand(1,4)),
			'validity' => '2020-06-02',
			'expire' => '2020-08-02',
			'direction' => [ 'id' => 1, 'name' => "Import", 'created_at' => null, 'updated_at' => null ]
		];
		/* Example */

		/*return [
		'id' => $this->id,
		'provider' => $this->provider,
		'type' => $this->type,
		'company_user_id' => $this->companyUser,
		'validity' => $this->validity,
		'expire' => $this->expire,
		'status' => $this->status,
		'gp_container' => $this->gpContainer,
		'port' => $this->inlandports


		];*/
	}
}
