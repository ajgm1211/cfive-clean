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

    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'quote_id' => $this->quote_id,
        'delivery_type' => is_null($this->delivery_type) ? $this->delivery_type : $this->delivery_type()->first(),
        'company_id' => $this->company,
        'contact_id' => is_null($this->contact_id) ? $this->contact_id : ['id'=>$this->contact_id,'company_id'=>$this->company()->first()->id,'name'=>$this->contact()->first()->getFullName()],
        'commodity' => $this->commodity,
        'status' => $this->status_quote()->first(),
        'type' => $this->type,
        'remarks' => $this->remarks,
        'remarks_spanish' => $this->remarks_spanish,
        'remarks_english' => $this->remarks_english,
        'remarks_portuguese' => $this->remarks_portuguese,
        'kind_of_cargo' => is_null($this->kind_of_cargo) ? $this->kind_of_cargo : $this->kind_of_cargo()->first(),
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
        'terms_english' => $this->terms_english,
        'terms_portuguese' => $this->terms_portuguese,
        'language_id' => $this->language()->first(),
        'incoterm_id' => is_null($this->incoterm_id) ? $this->incoterm_id : $this->incoterm()->first(),
        'rates' => $this->rates_v2()->get(),
        'gp_container' => $this->getContainerCodes($this->equipment,true),
        'client_currency'=>$this->user()->first()->companyUser()->first()->currency()->first()
      ];
    }
  }