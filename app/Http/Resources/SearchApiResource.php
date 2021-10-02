<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;
use App\GroupContainer;

class SearchApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $origin_ports =$this->formatData($this->origin_ports()->get(),$type=1);
        $destination_ports = $this->formatData($this->destination_ports()->get(),$type=1);

        $origin_address=$this->formatData($this->origin_locations()->get(),$type=2);
        $destination_address=$this->formatData($this->destination_locations()->get(),$type=2);
        
        $carriers = $this->carriers()->get()->map(function ($carrier) {
            return $carrier->only(['id', 'name', 'image']);
        });

        $api_providers = $this->api_providers()->get()->map(function ($carrier) {
            return $carrier->only(['id', 'name', 'image', 'code']);
        });

        $containers = $this->containers();

        if($containers != null && count($containers) != 0){
            $container_group = GroupContainer::where('id',$containers[0]['gp_container_id'])->first();
        }else{
            $container_group = null;
        }

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
            'carriers_api' => $api_providers,
            'company' => isset($this->company_id) ? $this->client_company()->first() : null,
            'contact' => $contact,
            'price_level' => isset($this->price_level_id) ? $this->price_level()->first() : null,
            'containers' => $containers,
            'container_group' => $container_group,
            'delivery_type' => isset($this->delivery) ? $this->delivery_type()->first() : null,
            'direction' => isset($this->direction) ? $this->direction()->first() : null,
            'origin_charges' => $this->origin_charges,
            'destination_charges' => $this->destination_charges,
            'origin_address' => isset($origin_address) ? $origin_address : null,
            'destination_address' => isset($destination_address) ? $destination_address : null,
            'show_rate_currency' => $this->show_rate_currency,
        ];
    }

    public function formatData($data,$identificator){
        $array=[];
        foreach($data as $key=>$info){
            if ($identificator==1) {
                $type='port';
                $location=$info['display_name'];   
            
            $array[$key]=[
                'id'=>$info['id'],
                'display_name'=>$location,
                'country'=>null,
                'location'=>$location,
                'type'=>$type
            ];
            }else{
                $type='city';
                $location=$info['name'];
                $array[$key]=[
                    'id'=>$info['id'],
                    'country'=>null,
                    'location'=>$location,
                    'type'=>$type
                ];
            }
        }

        return $array;
    }


}
