<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Container;

class InlandRangeResource extends JsonResource
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
            'lower' => $this->lower,
            'upper' => $this->upper,
            'details' => json_decode($this->details, true),
            'inland' => $this->inland,
            'currency' => $this->currency,
            'gp_container' => $this->gpContainer,
            'per_container' => $this->per_container()
             
          ];

        return $this->addContainers($data);
    }

    public function addContainers($data) 
    {
        $containers = json_decode($this->containers, true);

        foreach ($this->available_containers as $available_container) {
           $data['rates_'.$available_container] = isset($containers['C'.$available_container]) ? $containers['C'.$available_container] : '-';
        }

        return $data;
    }
}
