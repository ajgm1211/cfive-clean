<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarrierResource extends JsonResource
{

    protected $company_user;

    public function companyUser($value)
    {
        $this->company_user = $value;
        return $this;
    }

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
            'type' => 'carrier',
            'scac' => $this->scac,
            'url' => $this->image ? config('medialibrary.s3.domain')."/imgcarrier/".$this->image:$this->url,
            'referential_data' => ($this->company_user != null && $this->referentialData($this->company_user->id) != null) ?
             json_decode($this->referentialData($this->company_user->id)->json_data):[]
        ];
    }
}
