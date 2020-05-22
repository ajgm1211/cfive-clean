<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;

class OceanFreightResource extends JsonResource
{
    /**
     * @var
     */
    private $available_containers;
    
    public function __construct($resource)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;

        // Get the available containers except dry
        $this->available_containers = Container::where('gp_container_id', '!=', 1)->pluck('code');
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'origin' => $this->port_origin, 
            'destination' => $this->port_destiny,
            'carrier' => $this->carrier,
            'contract' => $this->contract,
            'currency' => $this->currency,
            'schedule_type' => $this->schedule_type,
            'transit_time' => $this->transit_time,
            'via' => $this->via
        ];

        return $this->addContainers($data);
    }

    public function addContainers($data) 
    {
        $data['rates_20DV'] = $this->twuenty;
        $data['rates_40DV'] = $this->forty;
        $data['rates_40HC'] = $this->fortyhc;
        $data['rates_40NOR'] = $this->fortynor;
        $data['rates_45HC'] = $this->fortyfive; 

        $containers = json_decode($this->containers, true);

        foreach ($this->available_containers as $available_container) {
           $data['rates_'.$available_container] = isset($containers['C'.$available_container]) ? $containers['C'.$available_container] : '-';
        }

        return $data;
    }
}
