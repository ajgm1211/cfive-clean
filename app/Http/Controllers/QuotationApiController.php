<?php

namespace App\Http\Controllers;

use App\IntegrationQuoteStatus;
use App\QuoteV2;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Traits\UtilTrait;
use Illuminate\Support\Facades\Auth;

class QuotationApiController extends Controller
{

    use UtilTrait;

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

        $quote = QuoteV2::NewQuoteSelect()->ConditionalWhen($type, $status, $integration)
            ->NewRateV2()->AuthUserCompany($company_user_id)
            ->NewUserRelation()->NewCompanyRelation()
            ->NewContactRelation()->IncotermRelation()->findOrFail($id);

        //Modify equipment array
        $this->transformEquipmentSingle($quote);

        return $quote;
    }
}
