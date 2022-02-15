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

        $created_at=$this->created_at;
        $fecha=$this->formatear($created_at);

        $origin_ports = $this->origin_harbor()->get();
        $destiny_ports = $this->destination_harbor()->get();
        $origin_array = [];
        $destiny_array = [];

        $containers = $this->getContainersFromEquipment($this->equipment);

        foreach($origin_ports as $item){
            array_push($origin_array, $item->display_name);
        }

        foreach($destiny_ports as $item){
            array_push($destiny_array, $item->display_name);
        }

        $origin_ports_duplicate = $origin_ports->unique();
        $destiny_ports_duplicate = $destiny_ports->unique();

        if(isset($this->contact_id)){
            $contact = $this->contact()->first();
            $fullName = $contact->getFullName();
            $contact->setAttribute('name', $fullName);
        }else{
            $contact = null;
        }

        if(isset($this->user_id)){
            $this->user->setAttribute('fullname', $this->user->fullname);
        }

        $inland_ports = $this->setInlandPorts();
        $local_ports = $this->setLocalPorts();

        return [
            'id' => $this->id,
            'quote_id' => $this->quote_id,
            'custom_quote_id' => $this->custom_quote_id,
            'delivery_type' => is_null($this->delivery_type) ? $this->delivery_type : $this->delivery_type()->first(),
            'company_id' => $this->company,
            'contact_id' => is_null($this->contact_id) ? $this->contact_id : ['id' => $this->contact_id, 'company_id' => $this->company_id, 'name' => $this->contact()->first()->getFullName()],
            'contact' => $contact,
            'commodity' => $this->commodity,
            'status' => $this->status_quote()->first(),
            'type' => $this->type,
            'remarks' => $this->remarks,
            'created_at'=> $fecha,
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
            'containers' => $containers,
            'cargo_type_id' => is_null($this->cargo_type_id) ? $this->cargo_type_id : $this->cargoType()->first()->name,
            'total_quantity' => $this->total_quantity,
            'total_weight' => $this->total_weight,
            'total_volume' => $this->total_volume,
            'chargeable_weight' => $this->chargeable_weight,
            'user_id' => $this->user,
            'payment_conditions' => $this->payment_conditions,
            'terms_and_conditions' => $this->terms_and_conditions,
            'terms_english' => $this->terms_english,
            'terms_portuguese' => $this->terms_portuguese,
            'pdf_options' => $this->pdf_options,
            'language_id' => $this->language()->first(),
            'incoterm_id' => is_null($this->incoterm_id) ? $this->incoterm_id : $this->incoterm()->first(),
            'custom_incoterm' => $this->custom_incoterm,
            'rates' => $this->rates_v2()->get(),
            'gp_container' => $this->getContainerCodes($this->equipment, true),
            'client_currency' => $this->user()->first()->companyUser()->first()->currency()->first(),
            'origin' => $origin_array ?? '--',
            'destiny' => $destiny_array ?? '--',
            'origin_ports' => $origin_ports ?? '--',
            'destiny_ports' => $destiny_ports ?? '--',
            'decimals' => $this->company_user()->first()->decimals,
            'local_charges' => $this->type == 'FCL' ? $this->local_charges : $this->local_charges_lcl,
            'price_id' => $this->price_id,
            'price_level' => $this->price()->first(),
            'carriers' => $this->carrier()->get(),
            'search_options' => $this->search_options,
            'direction_id' => $this->direction_id,
            'inland_ports' => $inland_ports,
            'local_ports' => $local_ports,
            'origin_ports_duplicate' => $origin_ports_duplicate,
            'destiny_ports_duplicate' => $destiny_ports_duplicate,
        ];
    }

    public function formatear($created_at)
    {
        $fecha=date('Y-m-d H:m:s', strtotime($created_at));

        return $fecha;
    }

    public function setInlandPorts()
    {
        $addresses = $this->inland_addresses()->get();

        $ports = ['origin' => [], 'destination' => []];

        foreach($addresses as $address){
            if($address->type = 'Origin'){
                array_push($ports['origin'], $address->port()->first()->id);
            }else if($address->type = 'Destination'){
                array_push($ports['destination'], $address->port()->first()->id);
            }
        }

        return $ports;
    }

    public function setLocalPorts()
    {   
        if($this->type == "FCL"){
            $locals = $this->local_charges()->get();
        }elseif($this->type == "LCL"){
            $locals = $this->local_charges_lcl()->get();
        }elseif($this->type == "AIR"){
            $locals = [];
        }

        $ports = ['origin' => [], 'destination' => [], 'originCharges' => [], 'destinationCharges' => [] ];

        foreach($locals as $local){
            if($local->type_id == 1){
                if(!in_array($local->port->first,$ports['origin'])){
                    array_push($ports['origin'], $local->port()->first());
                }
            }else if($local->type_id == 2){
                if(!in_array($local->port->first,$ports['destination'])){
                    array_push($ports['destination'], $local->port()->first());
                }
            }
        }

        $charges = $this->charge()->get();
        foreach($charges as $charge) {
            if($charge->type_id == 1) {
                if(!in_array($charge->automatic_rate()->first()->origin_port()->first(), $ports['originCharges'])){
                    array_push($ports['originCharges'], $charge->automatic_rate()->first()->origin_port()->first());
                }
            }
            if($charge->type_id == 2) {
                if(!in_array($charge->automatic_rate()->first()->destination_port()->first(), $ports['destinationCharges'])){
                    array_push($ports['destinationCharges'], $charge->automatic_rate()->first()->destination_port()->first());
                }
            }
            
        }

        return $ports;
    }
}
