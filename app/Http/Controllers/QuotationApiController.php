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

        $query = QuoteV2::NewQuoteSelect()->ConditionalWhen($type, $status, $integration)
            ->AuthUserCompany($company_user_id)
            ->RateV2()->UserRelation()->NewCompanyRelation()
            ->NewContactRelation()->PriceRelation()
            ->IncotermRelation()->orderBy('created_at', 'desc');

        if ($request->paginate) {
            $quotes = $query->paginate($request->paginate);
        } else {
            $quotes = $query->take($request->size)->get();
        }

        //Modify equipment array
        $this->transformEquipment($quotes);

        //Update Integration Quote Status
        if ($integration) {
            foreach ($quotes as $quote) {
                IntegrationQuoteStatus::where('quote_id', $quote->id)->update(['status' => 1]);
            }
        }

        $collection = Collection::make($quotes);

        if (!$request->paginate) {
            $collection->transform(function ($quote, $key) {
                unset($quote['origin_port_id']);
                unset($quote['destination_port_id']);
                unset($quote['origin_address']);
                unset($quote['destination_address']);
                unset($quote['currency_id']);
                return $quote;
            });
        }

        return $quotes;
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

        $quote = $quote->makeHidden(['incoterm_id', 'contact_id', 'company_id'])->toArray();
        
        $data = compact(
            'quote',
            'ocean_freight',
            'origin_charges',
            'destination_charges',
            'inlands'
        );

        return response()->json(['data' => $data]);
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
