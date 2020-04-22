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
    
    public function __construct($resource, $available_containers)
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
        $data['20DV'] = $this->twuenty;
        $data['40DV'] = $this->forty;
        $data['40HC'] = $this->fortyhc;
        $data['40NOR'] = $this->fortynor;
        $data['45HC'] = $this->fortyfive; 

        foreach ($this->available_containers as $container) {
           $data[$container] = isset($this->containers['C'.$container]) ? $this->containers['C'.$container] : '-';
        }

        return $data;
    }
}
