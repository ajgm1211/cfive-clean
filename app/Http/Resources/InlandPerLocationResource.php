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
        $containers=json_encode($this->json_container);

        $data = [
            'container' => $containers,
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
        foreach ($this->available_containers as $available_container) {
            $data['rates_'.$available_container] = isset($this->json_container['C'.$available_container]) ? $this->json_container['C'.$available_container] : '-';
        }

        return $data;
    }
}