<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractLclResource extends JsonResource
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
            'number' => $this->number,
            'company_user' => $this->companyUser,
            'user_name' => $this->user->fullname ?? $this->user_from_request[0]->fullname ?? "---",
            'direction' => $this->direction ?? [],
            'status' => $this->status,
            'validity' => $this->validity,
            'expire' => $this->expire,
            'created_at' => date('Y-m-d H:m:s', strtotime($this->created_at)),
            'remarks' => $this->remarks ? $this->remarks : '',
            'carriers' => $this->carriers->pluck('carrier'),
            'restrictions' => [
                'companies' => $this->company_restriction,
                'users' => $this->user_restriction,
            ],
        ];
    }
}
