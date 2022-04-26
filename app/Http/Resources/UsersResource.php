<?php

namespace App\Http\Resources;

use App\CompanyUser;
use App\SettingsWhitelabel;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = array(
            'id' => $this->id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'options' => $this->option,
            'company_user' => $this->getCompanyUser($this->company_user_id) ?? null,
            'name_company' => $this->name_company,
            'position' => $this->position,
            'access' => $this->access,
            'verified' => $this->verified,
            'state' => $this->state,
            'api_token' => $this->api_token,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'whitelabel' => $this->whitelabel,
            'settings_whitelabel' => $this->getSettingsWhitelabel($this->company_user_id) ?? null,
        );

        return $data;
        //return parent::toArray($request);
    }

    public function getCompanyUser($id){
        return CompanyUser::find($id);
    }
    public function getSettingsWhitelabel($company_user_id){
        return SettingsWhitelabel::where('company_user_id', $company_user_id)->first();
    }
}
