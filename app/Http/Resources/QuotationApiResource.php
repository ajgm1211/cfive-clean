<?php

namespace App\Http\Resources;

use App\AutomaticInland;
use App\AutomaticRate;
use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
use App\Http\Traits\QuotationApiTrait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteLcl;

class QuotationApiResource extends JsonResource
{
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
            'type' => $this->type,
            'quote_id' => $this->quote_id,
            'custom_quote_id' => $this->custom_quote_id,
            'equipment' => $this->transformEquipmentSingle($this->equipment) ?? null,
            "valid_from" => $this->validity_start,
            "valid_until" => $this->validity_end,
            "date_issued" => $this->date_issued,
            "status" => $this->status,
            "incoterm" => $this->incoterm->name ?? null,
            "delivery" => $this->delivery,
            "cargo_type" => $this->cargoType()->first()->name ?? null,
            "total_quantity" => $this->total_quantity,
            "total_weight" => $this->total_weight,
            "total_volume" => $this->total_volume,
            "chargeable_weight" => $this->chargeable_weight,
            "kind_of_cargo" => $this->kind_of_cargo,
            "gdp" => $this->gdp,
            "risk_level" => $this->risk_level,
            "commodity" => $this->commodity,
            "spanish_remarks" => $this->remarks_spanish,
            "english_remarks" => $this->remarks_english,
            "portuguese_remarks" => $this->remarks_portuguese,
            "localcharge_remarks" => $this->localcharge_remarks,
            "spanish_terms_conditions" => $this->terms_and_conditions,
            "english_terms_conditions" => $this->terms_english,
            "portuguese_terms_conditions" => $this->terms_portuguese,
            "payment_conditions" => $this->payment_conditions,
            "owner" => $this->company_user->name ?? null,
            "created_by" => $this->user->fullname ?? null,
            "created_at" => $this->created_at->toDateTimeString(),
            "updated_at" => $this->updated_at->toDateTimeString(),
            "company" => $this->company()->select('business_name', 'address', 'phone', 'options')->first() ?? null,
            "contact" => $this->contact()->select('first_name', 'last_name', 'email', 'phone')->first() ?? null,
            'ocean_freight' => QuotationOceanFreightResource::collection($this->rates_v2()->SelectFields()->SelectChargeApi($this->type)->CarrierRelation()->get()),
            'origin_charges' => QuotationLocalChargeResource::collection($this->localCharges($this->id, 1, $this->type)),
            'destination_charges' => QuotationLocalChargeResource::collection($this->localCharges($this->id, 2, $this->type)),
            'inlands' => $this->type == 'FCL' ? QuotationInlandResource::collection($this->inland()->SelectFields()->get()):QuotationInlandLclResource::collection($this->inland_lcl()->SelectFields()->get()),
        ];

        return $data;
    }

    public function transformEquipmentSingle($equipment)
    {
        $containers = Container::select('id', 'code')->get();

        $array = array();
        foreach (json_decode($equipment) as $val) {
            if ($val == '20') {
                $val = 1;
            } elseif ($val == '40') {
                $val = 2;
            } elseif ($val == '40HC') {
                $val = 3;
            } elseif ($val == '45HC') {
                $val = 4;
            } elseif ($val == '40NOR') {
                $val = 5;
            }

            foreach ($containers as $cont) {
                if ($val == $cont->id) {
                    array_push($array, $cont->code);
                    $equipment = $array;
                }
            }
        }

        return $equipment;
    }

    public function localCharges($id, $type, $quote_type)
    {
        switch($quote_type){
            case 'FCL':
                $localcharges = LocalChargeQuote::select('id', 'price', 'profit', 'total', 'charge', 'currency_id', 'port_id', 'calculation_type_id')
                ->Quote($id)->GetPort()->Type($type)->get();
            break;
            case 'LCL':
                $localcharges = LocalChargeQuoteLcl::select('id', 'price', 'units', 'total', 'charge', 'currency_id', 'port_id', 'calculation_type_id')
                ->Quote($id)->GetPort()->Type($type)->get();
            break;
        }

        return $localcharges;
    }
}
