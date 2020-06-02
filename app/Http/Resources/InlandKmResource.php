<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;

class InlandKmResource extends JsonResource
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

        // Get the available containers
        $this->available_containers = Container::get()->pluck('code');
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
            'per_container' => $this->per_container()
             
          ];

        return $this->addContainers($data);
    }

    public function addContainers($data) 
    {
        foreach ($this->available_containers as $available_container) {
           $data['rates_'.$available_container] = isset($this->json_containers['C'.$available_container]) ? $this->json_containers['C'.$available_container] : '-';
        }

        return $data;
    }
}
