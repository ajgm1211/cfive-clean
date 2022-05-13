<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CarrierResource;
use App\Http\Traits\UtilTrait;

class QuotationOceanFreightResource extends JsonResource
{

    use UtilTrait;

    protected $segment_id;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return $this->resource->map(function($item){
                return [
                'id' => $item->id,
                'segment_id'=> $this->segment_id,
                'contract' => $item->contract,
                'valid_from' => $item->valid_from,
                'valid_until' => $item->valid_until,
                'profit' => $this->arrayToFloat($item->profit) ?? [],
                'total' => $this->arrayToFloat(json_decode($item->total)) ?? [],
                'currency' => $item->currency->alphacode ?? null,
                'origin' => $item->origin_port->display_name ?? null,
                'destiny' => $item->destination_port->display_name ?? null,
                'transit_time' => (int) $item->transit_time ?? null,
                'service' =>  $this->getScheduleType($item->schedule_type) ?? null,
                'via' => $item->via ?? null,
                'carrier' => (new CarrierResource($item->carrier))->companyUser($item->quoteV2->company_user),
                'all_in' => $item->quoteV2->pdf_options['allIn'] ?? false,
                //'carrier' => $this->carrier,
                'charges' => count($item->charge)>0 ? QuotationOceanFreightChargeResource::collection($item->charge):QuotationOceanFreightChargeLclResource::collection($item->charge_lcl_air),
            ];
        });
        
    }

    

    public function segmentId ($value)
    {
        $this->segment_id = $value;
        return $this;
    }


    public function getScheduleType($value)
    {

            if ($value == '1') {
                $val = 'Direct';
            } elseif ($value == '2') {
                $val = 'Transfer';
            } elseif ($value != '') {
                $val = $value;
            } else {
                $val = "";
            } 

        return $val;
    }

}
