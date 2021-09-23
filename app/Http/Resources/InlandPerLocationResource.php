<?php

namespace App\Http\Resources;

use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class InlandPerLocationResource extends JsonResource
{

    private $available_containers;
    public function __construct($resource)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        // Get the available containers except dry
        $this->available_containers = Container::all()->pluck('code');
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
            'currency' => $this->currency,
            'port' => $this->harbor,
            'inland' => $this->inland_id,
            'location' => $this->location,
            'type' => $this->type,
            'service'=>$this->service,
        ];

        return $this->addContainers($data);
    }
    public function addContainers($data)
    {
        $containers = $this->json_containers;
        
        foreach ($this->available_containers as $available_container) {
            
            $data['rates_' . $available_container] = isset($containers['C' . $available_container]) ? $containers['C' . $available_container] : '-';
        }
        return $data;
    }
}