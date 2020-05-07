<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
      'provider' => $this->provider,
      'type' => $this->type,
      'company_user_id' => $this->companyUser,
      'validity' => $this->validity,
      'expire' => $this->expire,
      'status' => $this->status,
      'gp_container' => $this->gpContainer,
      

    ];
  }
}
