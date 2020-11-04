<?php

namespace App\Http\Controllers;

use App\AutomaticInland;
use App\Container;
use App\Http\Traits\QuotationApiTrait;
use App\IntegrationQuoteStatus;
use App\QuoteV2;
use App\AutomaticRate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Traits\UtilTrait;
use Illuminate\Support\Facades\Auth;

class QuotationApiController extends Controller
{

    use UtilTrait;
    use QuotationApiTrait;

    /**
     * Show quotes list
     * @param Request $request 
     * @return JSON
     */

    public function list(Request $request)
    {

        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;
        $company_user_id = Auth::user()->company_user_id;

        $quotes = QuoteV2::NewQuoteSelect()->AuthUserCompany($company_user_id)->NewCompanyRelation()
            ->NewContactRelation()->IncotermRelation()->get();

        $containers = Container::all();

        $array = array();

        foreach ($quotes as $quote) {
            $freight_charges = AutomaticRate::SelectFields()
                ->SelectCharge()->CarrierRelation()->where('quote_id', $quote->id)->get();

            $ocean_freight = $this->mapFreightCharges($freight_charges);

            $origin_charges = $this->localCharges($quote, 1);

            $destination_charges = $this->localCharges($quote, 2);

            $inlands = AutomaticInland::SelectFields()->where('quote_id', $quote->id)->get();

            $inlands = $this->mapInlandCharges($inlands);

            //Modify equipment array
            $this->transformEquipmentSingle($quote);

            $basic_info = $this->transformQuoteInfo($quote);

            $data = compact(
                'basic_info',
                'ocean_freight',
                'origin_charges',
                'destination_charges',
                'inlands'
            );

            array_push($array, $data);
        }

        return response()->json(['quotes' => $array]);
    }

    /**
     * Show quotes by ID
     * @param Request $request 
     * @return JSON
     */

    public function retrieve(Request $request, $id)
    {
        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;
        $company_user_id = Auth::user()->company_user_id;

        $quote = QuoteV2::NewQuoteSelect()->NewCompanyRelation()
            ->NewContactRelation()->IncotermRelation()->findOrFail($id);

        $containers = Container::all();

        $freight_charges = AutomaticRate::SelectFields()
            ->SelectCharge()->CarrierRelation()->where('quote_id', $quote->id)->get();

        $ocean_freight = $this->mapFreightCharges($freight_charges);

        $origin_charges = $this->localCharges($quote, 1);

        $destination_charges = $this->localCharges($quote, 2);

        $inlands = AutomaticInland::SelectFields()->where('quote_id', $id)->get();

        $inlands = $this->mapInlandCharges($inlands);

        //Modify equipment array
        $this->transformEquipmentSingle($quote);

        $basic_info = $this->transformQuoteInfo($quote);

        $data = compact(
            'basic_info',
            'ocean_freight',
            'origin_charges',
            'destination_charges',
            'inlands'
        );

        return response()->json(['quote' => $data]);
    }

    public function mapFreightCharges($collection)
    {
        $collection->map(function ($value) {
            $value['currency_code'] = $value->currency->alphacode;
            $value['origin'] = $value->origin_port->display_name;
            $value['destiny'] = $value->destination_port->display_name;
            unset($value['origin_port_id']);
            unset($value['destination_port_id']);
            unset($value['origin_address']);
            unset($value['destination_address']);
            unset($value['currency_id']);
            unset($value['currency']);
            unset($value['origin_port']);
            unset($value['destination_port']);
            unset($value['carrier_id']);
            return $value;
        });

        return $collection;
    }

    public function transformQuoteInfo($quote)
    {
        $value = $quote->makeHidden(['incoterm_id', 'contact_id', 'company_id'])->toArray();

        $value['incoterm'] = $quote->incoterm->name ?? null;

        return $value;
    }

    public function mapInlandCharges($collection)
    {
        $collection->map(function ($value) {
            $value['currency_code'] = $value->currency->alphacode;
            $value['port_name'] = $value->port->display_name;
            $value['provider'] = $value->providers->name ?? null;
            unset($value['port_id']);
            unset($value['currency_id']);
            unset($value['provider_id']);
            unset($value['port']);
            unset($value['currency']);
            unset($value['providers']);
            return $value;
        });

        return $collection;
    }
}
