<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    // ADD RATES CALL
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'quote_id' => $this->quote_id,
        'service' => $this->delivery_type,
        'customer' => $this->company_id,
        'company_id' => $this->company,
        'contact' => $this->contact_id,
        'contact_id' => $this->contact,
        'commodity' => $this->commodity,
        'status' => $this->status,
        'type' => $this->type,
        'cargo' => $this->kind_of_cargo,
        'company_user' => $this->companyUser,
        'company_user_id' => $this->company_user_id,
        'origin_address' => $this->origin_address,
        'destination_address' => $this->destination_address,
        'issued' => $this->date_issued,
        'user_id' => $this->user_id,
        'validity' => $this->validity_end,
        'equipment' => $this->equipment,
        'owner' => $this->user,
        'payment_conditions' => $this->payment_conditions,
        'terms_and_conditions' => $this->terms_and_conditions,
        'incoterm_id' => $this->incoterm,
        'language' => $this->language
        //'gp_container' => $this->gpContainer ?? [ 'id' => 1, 'name' => 'DRY' ],
        //'ports' => $this->inlandports->pluck('ports'),
      ];
    }
}
