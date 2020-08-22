<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;
use App\Incoterm;
use App\DeliveryType;
use App\Contact;

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
        'delivery_type' => ['id'=>$this->delivery_type, 'name'=>$this->delivery_type()->first()->name],
        'company_id' => $this->company,
        'contact_id' => is_null($this->contact_id) ? $this->contact_id : ['id'=>$this->contact_id,'company_id'=>$this->company()->first()->id,'name'=>$this->contact()->first()->getFullName()],
        'commodity' => $this->commodity,
        'status' => ['id'=>1,'name'=>$this->status],
        'type' => $this->type,
        'kind_of_cargo' => is_null($this->kind_of_cargo) ? $this->kind_of_cargo : ['name'=>$this->kind_of_cargo],
        'company_user' => $this->companyUser,
        'company_user_id' => $this->company_user_id,
        'origin_address' => $this->origin_address,
        'destination_address' => $this->destination_address,
        'validity_start' => $this->validity_start,
        'validity_end' => $this->validity_end,
        'equipment' => $this->getContainerCodes($this->equipment),
        'user_id' => $this->user,
        'payment_conditions' => $this->payment_conditions,
        'terms_and_conditions' => $this->terms_and_conditions,
        'language' => $this->language,
        'incoterm_id' => is_null($this->incoterm_id) ? $this->incoterm_id : ['id'=>$this->incoterm_id,'name'=>$this->incoterm()->first()->name]
        //'gp_container' => $this->gpContainer ?? [ 'id' => 1, 'name' => 'DRY' ],
        //'ports' => $this->inlandports->pluck('ports'),
      ];
    }
  }