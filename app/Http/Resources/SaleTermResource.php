<?php

namespace App\Http\Resources;
use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleTermResource extends JsonResource
{


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
			'name' => $this->name,
			'type_id' => $this->type_id,
			'group_container_id' => $this->group_container_id,
			'port_id' => $this->port_id,
			'company_user_id' => $this->company_user_id,
			'company_user' => $this->companyUser,
			'type' => $this->type,
			'group_container' => $this->group_container  ?? [ 'id' => 1, 'name' => 'DRY' ],
			'port' => $this->port,
			
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
