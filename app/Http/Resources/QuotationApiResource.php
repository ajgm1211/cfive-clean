<?php

namespace App\Http\Resources;

use App\AutomaticInland;
use App\AutomaticRate;
use App\Charge;
use App\Container;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\UtilTrait;
use App\Http\Traits\QuotationApiTrait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteLcl;
use App\CompanyUserQuoteSegment;
use Spatie\MediaLibrary\Models\Media;

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
        $id =$this->company_user->id;
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
            "custom_incoterm" => $this->custom_incoterm ?? null,
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
            "spanish_remarks" => stripslashes($this->utf8_ansi($this->remarks_spanish)),
            "english_remarks" => stripslashes($this->utf8_ansi($this->remarks_english)),
            "portuguese_remarks" => stripslashes($this->utf8_ansi($this->remarks_portuguese)),
            "localcharge_remarks" => stripslashes($this->utf8_ansi($this->localcharge_remarks)),
            "spanish_terms_conditions" => stripslashes($this->utf8_ansi($this->terms_and_conditions)),
            "english_terms_conditions" => stripslashes($this->utf8_ansi($this->terms_english)),
            "portuguese_terms_conditions" => stripslashes($this->utf8_ansi($this->terms_portuguese)),
            "payment_conditions" => $this->payment_conditions,
            "owner_user_id" => $this->user_id ?? null,
            "owner_id" => $this->company_user->id ?? null,
            "owner" => $this->company_user->name ?? null,
            "pdf_url" => $this->getMedia('document')->first() ? $this->getMedia('document')->first()->getUrl():null,
            "created_by" => $this->user->fullname ?? null,
            "created_at" => $this->created_at->toDateTimeString(),
            "updated_at" => $this->updated_at->toDateTimeString(),
            "company" => $this->company()->select('id', 'business_name', 'address', 'phone', 'options')->first() ?? null,
            "contact" => $this->contact()->select('id', 'first_name', 'last_name', 'email', 'phone')->first() ?? null,
            "exchange_rates" => $this->pdf_options["exchangeRates"] ?? null,
            'ocean_freight' => (new QuotationOceanFreightResource($this->rates_v2()->SelectFields()->SelectChargeApi($this->type)->CarrierRelation()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,1)),
            'origin_charges' => (new QuotationLocalChargeResource($this->localCharges($this->id, 1, $this->type)))->segmentId($this->getSegmentIdByType($this->company_user->id,2)),
            'destination_charges' => (new QuotationLocalChargeResource($this->localCharges($this->id, 2, $this->type)))->segmentId($this->getSegmentIdByType($this->company_user->id,3)),
            'inlands' => $this->type == 'FCL' ? (new QuotationInlandResource($this->inland()->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,4)) : (new QuotationInlandLclResource($this->inland_lcl()->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,4)),
            'original_origin_charges' => $this->type == 'FCL' ? (new QuotationChargeResource($this->charge()->where('charges.type_id', 1)->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,2)) : (new QuotationChargeLclResource($this->charge_lcl()->where('charge_lcl_airs.type_id', 1)->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,2)),
            'original_destination_charges' => $this->type == 'FCL' ? (new QuotationChargeResource($this->charge()->where('charges.type_id', 2)->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,3)) : (new QuotationChargeLclResource($this->charge_lcl()->where('charge_lcl_airs.type_id', 2)->SelectFields()->get()))->segmentId($this->getSegmentIdByType($this->company_user->id,3)),
        ];

        return $data;
    }

    /**
     * Adjust equipment formats
     *
     * @param  mixed $equipment
     * @return void
     */
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

    /**
     * Get local charges' data from DB
     *
     * @param  mixed $id
     * @param  mixed $type
     * @param  mixed $quote_type
     * @return void
     */
    public function localCharges($id, $type, $quote_type)
    {
        switch ($quote_type) {
            case 'FCL':
                $localcharges = LocalChargeQuote::select('id', 'price', 'profit', 'total', 'charge', 'currency_id', 'port_id', 'calculation_type_id', 'provider_name', 'surcharge_id', 'quote_id', 'sale_term_code_id', 'source')
                    ->Quote($id)->GetPort()->Type($type)->get();
                break;
            case 'LCL':
                $localcharges = LocalChargeQuoteLcl::select('id', 'price', 'profit', 'units', 'total', 'charge', 'currency_id', 'port_id', 'calculation_type_id', 'provider_name', 'surcharge_id', 'quote_id', 'sale_term_code_id')
                    ->Quote($id)->GetPort()->Type($type)->get();
                break;
        }

        return $localcharges;
    }

    public static function utf8_ansi($valor='') {

        $utf8_ansi2 = array(
        "\u00c0" =>"??",
        "\u00c1" =>"??",
        "\u00c2" =>"??",
        "\u00c3" =>"??",
        "\u00c4" =>"??",
        "\u00c5" =>"??",
        "\u00c6" =>"??",
        "\u00c7" =>"??",
        "\u00c8" =>"??",
        "\u00c9" =>"??",
        "\u00ca" =>"??",
        "\u00cb" =>"??",
        "\u00cc" =>"??",
        "\u00cd" =>"??",
        "\u00ce" =>"??",
        "\u00cf" =>"??",
        "\u00d1" =>"??",
        "\u00d2" =>"??",
        "\u00d3" =>"??",
        "\u00d4" =>"??",
        "\u00d5" =>"??",
        "\u00d6" =>"??",
        "\u00d8" =>"??",
        "\u00d9" =>"??",
        "\u00da" =>"??",
        "\u00db" =>"??",
        "\u00dc" =>"??",
        "\u00dd" =>"??",
        "\u00df" =>"??",
        "\u00e0" =>"??",
        "\u00e1" =>"??",
        "\u00e2" =>"??",
        "\u00e3" =>"??",
        "\u00e4" =>"??",
        "\u00e5" =>"??",
        "\u00e6" =>"??",
        "\u00e7" =>"??",
        "\u00e8" =>"??",
        "\u00e9" =>"??",
        "\u00ea" =>"??",
        "\u00eb" =>"??",
        "\u00ec" =>"??",
        "\u00ed" =>"??",
        "\u00ee" =>"??",
        "\u00ef" =>"??",
        "\u00f0" =>"??",
        "\u00f1" =>"??",
        "\u00f2" =>"??",
        "\u00f3" =>"??",
        "\u00f4" =>"??",
        "\u00f5" =>"??",
        "\u00f6" =>"??",
        "\u00f8" =>"??",
        "\u00f9" =>"??",
        "\u00fa" =>"??",
        "\u00fb" =>"??",
        "\u00fc" =>"??",
        "\u00fd" =>"??",
        "\u00ff" =>"??");
    
        return strtr($valor, $utf8_ansi2);      
    
    }

    public function getSegmentIdByType($company_user_id, $type){
        $resultSegment = CompanyUserQuoteSegment::where('company_user_id', $company_user_id)->where('quote_segment_type_id', $type)->first();
        $result = !empty($resultSegment) ?  $resultSegment->segment_id : null;
        return $result;
    }
}
