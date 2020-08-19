<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleTermChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'id' => $this->id,
			'name' => $this->name,
			'amount' => $this->amount,
			'sale_term_id' => $this->sale_term_id,
			'calculation_type_id' => $this->calculation_type_id,
			'currency_id' => $this->currency_id,
			'sale_term' => $this->sale_term,
			'calculation_type' => $this->calculation_type,
			'currency' => $this->currency,
			'sale_term_code_id' => $this->sale_term_code_id,
			'sale_term_code' => $this->sale_term_code,
		];
    }
}
