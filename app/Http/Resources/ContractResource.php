<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;
class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $created_at=$this->created_at;
        $fecha=$this->formatear($created_at);
        $user_id=$this->user_id;
        $fullname=$this->fullname($user_id);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'company_user' => $this->companyUser,
            'user_name' => $fullname,
            'direction' => $this->direction,
            'status' => $this->status,
            'validity' => $this->validity,
            'expire' => $this->expire,
            'created_at'=> $fecha,
            'remarks' => $this->remarks ? $this->remarks : '',
            'carriers' => $this->carriers->pluck('carrier'),
            'restrictions' => [
                'companies' => $this->contract_company_restriction->pluck('company'),
                'users' => $this->contract_user_restriction->pluck('user'),
            ],
            'gp_container' => $this->gpContainer ? $this->gpContainer : ['id' => 1, 'name' => 'DRY'],
        ];
    }

    public function formatear($created_at){

        $fecha=date('Y-m-d H:m:s', strtotime($created_at));

        return $fecha;
    }
    public function fullname($user_id){

        if(isset($user_id)){
            $user=User::find($user_id);
            $fullname=$user->name." ".$user->lastname;
        }else{
            $fullname='----';
        }

        return $fullname;
    }
}
