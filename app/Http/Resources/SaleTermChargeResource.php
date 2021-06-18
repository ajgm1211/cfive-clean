<?php

namespace App\Http\Resources;

use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleTermChargeResource extends JsonResource
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
			'amount' => $this->amount,
			'sale_term_id' => $this->sale_term_id,
			'calculation_type_id' => $this->calculation_type_id,
			'currency_id' => $this->currency_id,
			'sale_term' => $this->sale_term,
			'calculation_type' => $this->calculation_type,
			'currency' => $this->currency,
			'sale_term_code_id' => $this->sale_term_code_id,
			'sale_term_code' => $this->sale_term_code,
			'total' => $this->json_containers,
		];

		return $this->addContainers($data);
	}

	public function addContainers($data)
	{
		$containers = $this->total;
		
		foreach ($this->available_containers as $available_container) {
			$data['rates_' . $available_container] = isset($containers['c' . $available_container]) ? $containers['c' . $available_container] : '-';
		}

		return $data;
	}
}
