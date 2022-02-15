<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchApiLclResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $origin_ports = $this->origin_ports()->get();
        $destination_ports = $this->destination_ports()->get();
        
        $carriers = $this->carriers()->get()->map(function ($carrier) {
            return $carrier->only(['id', 'name', 'image']);
        });

        if(isset($this->contact_id)){
            $contact = $this->contact()->first();
            $fullName = $contact->getFullName();
            $contact->setAttribute('name', $fullName);
        }else{
            $contact = null;
        }

        return [
            'id' => $this->id,
            'equipment' => $this->equipment,
            'pick_up_date' => $this->pick_up_date,
            'start_date' => rtrim(explode('/', $this->pick_up_date)[0]),
            'end_date' => ltrim(explode('/', $this->pick_up_date)[1]),
            'delivery_id' => $this->delivery,
            'type' => $this->type,
            'direction_id' => $this->direction,
            'company_user_id' => $this->company_user_id,
            'user_id' => $this->user_id,
            'contact_id' => $this->contact_id,
            'price_level_id' => $this->price_level_id,
            'company_id' => $this->company_id,
            'origin_ports' => $origin_ports,
            'destination_ports' => $destination_ports,
            'carriers' => $carriers,
            'company' => isset($this->company_id) ? $this->client_company()->first() : null,
            'contact' => $contact,
            'price_level' => isset($this->price_level_id) ? $this->price_level()->first() : null,
            'delivery_type' => isset($this->delivery) ? $this->delivery_type()->first() : null,
            'direction' => isset($this->direction) ? $this->direction()->first() : null,
            'origin_charges' => $this->origin_charges,
            'destination_charges' => $this->destination_charges,
            'origin_address' => $this->origin_address,
            'destination_address' => $this->destination_address,
        ];
    }
}
