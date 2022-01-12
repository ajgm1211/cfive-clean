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
            'ocean_freight' => QuotationOceanFreightResource::collection($this->rates_v2()->SelectFields()->SelectChargeApi($this->type)->CarrierRelation()->get()),
            'origin_charges' => QuotationLocalChargeResource::collection($this->localCharges($this->id, 1, $this->type)),
            'destination_charges' => QuotationLocalChargeResource::collection($this->localCharges($this->id, 2, $this->type)),
            'inlands' => $this->type == 'FCL' ? QuotationInlandResource::collection($this->inland()->SelectFields()->get()) : QuotationInlandLclResource::collection($this->inland_lcl()->SelectFields()->get()),
            'original_origin_charges' => $this->type == 'FCL' ?
                QuotationChargeResource::collection($this->charge()->where('charges.type_id', 1)->SelectFields()->get()) : QuotationChargeLclResource::collection($this->charge_lcl()->where('charge_lcl_airs.type_id', 1)->SelectFields()->get()),
            'original_destination_charges' => $this->type == 'FCL' ?
                QuotationChargeResource::collection($this->charge()->where('charges.type_id', 2)->SelectFields()->get()) : QuotationChargeLclResource::collection($this->charge_lcl()->where('charge_lcl_airs.type_id', 2)->SelectFields()->get()),
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
                $localcharges = LocalChargeQuoteLcl::select('id', 'price', 'units', 'total', 'charge', 'currency_id', 'port_id', 'calculation_type_id', 'provider_name', 'quote_id', 'sale_term_code_id')
                    ->Quote($id)->GetPort()->Type($type)->get();
                break;
        }

        return $localcharges;
    }

    public static function utf8_ansi($valor='') {

        $utf8_ansi2 = array(
        "\u00c0" =>"À",
        "\u00c1" =>"Á",
        "\u00c2" =>"Â",
        "\u00c3" =>"Ã",
        "\u00c4" =>"Ä",
        "\u00c5" =>"Å",
        "\u00c6" =>"Æ",
        "\u00c7" =>"Ç",
        "\u00c8" =>"È",
        "\u00c9" =>"É",
        "\u00ca" =>"Ê",
        "\u00cb" =>"Ë",
        "\u00cc" =>"Ì",
        "\u00cd" =>"Í",
        "\u00ce" =>"Î",
        "\u00cf" =>"Ï",
        "\u00d1" =>"Ñ",
        "\u00d2" =>"Ò",
        "\u00d3" =>"Ó",
        "\u00d4" =>"Ô",
        "\u00d5" =>"Õ",
        "\u00d6" =>"Ö",
        "\u00d8" =>"Ø",
        "\u00d9" =>"Ù",
        "\u00da" =>"Ú",
        "\u00db" =>"Û",
        "\u00dc" =>"Ü",
        "\u00dd" =>"Ý",
        "\u00df" =>"ß",
        "\u00e0" =>"à",
        "\u00e1" =>"á",
        "\u00e2" =>"â",
        "\u00e3" =>"ã",
        "\u00e4" =>"ä",
        "\u00e5" =>"å",
        "\u00e6" =>"æ",
        "\u00e7" =>"ç",
        "\u00e8" =>"è",
        "\u00e9" =>"é",
        "\u00ea" =>"ê",
        "\u00eb" =>"ë",
        "\u00ec" =>"ì",
        "\u00ed" =>"í",
        "\u00ee" =>"î",
        "\u00ef" =>"ï",
        "\u00f0" =>"ð",
        "\u00f1" =>"ñ",
        "\u00f2" =>"ò",
        "\u00f3" =>"ó",
        "\u00f4" =>"ô",
        "\u00f5" =>"õ",
        "\u00f6" =>"ö",
        "\u00f8" =>"ø",
        "\u00f9" =>"ù",
        "\u00fa" =>"ú",
        "\u00fb" =>"û",
        "\u00fc" =>"ü",
        "\u00fd" =>"ý",
        "\u00ff" =>"ÿ");
    
        return strtr($valor, $utf8_ansi2);      
    
    }
}
