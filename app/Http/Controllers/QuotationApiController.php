<?php

namespace App\Http\Controllers;

use App\AutomaticInland;
use App\Container;
use App\Http\Traits\QuotationApiTrait;
use App\IntegrationQuoteStatus;
use App\QuoteV2;
use App\AutomaticRate;
use App\Http\Resources\QuotationApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Traits\UtilTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\MixPanelTrait;

class QuotationApiController extends Controller
{

    use UtilTrait, MixPanelTrait;
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
        $costs = $request->costs;
        $paginate = 100;

        if ($request->paginate) {
            $paginate = $request->paginate;
        }

        $company_user_id = Auth::user()->company_user_id;

        $quotes = QuoteV2::ConditionalWhen($type, $status, $integration)->FilterByType()->AuthUserCompany($company_user_id)->paginate($paginate);

        //Update Integration Quote Status
        if ($integration) {
            $this->updateIntegrationStatus($quotes);
        }

        $this->trackEvents('api_quotes_v2', [], "api");

        return QuotationApiResource::collection($quotes, $costs);
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

        $quote = QuoteV2::ConditionalWhen($type, $status, $integration)
            ->AuthUserCompany($company_user_id)->findOrFail($id);

        $data = new QuotationApiResource($quote);

        $this->trackEvents('api_quotes_v2_by_id', [], "api");

        return $data;
    }

    public function updateIntegrationStatus($quotes)
    {
        foreach ($quotes as $quote) {
            IntegrationQuoteStatus::where('quote_id', $quote->id)->update(['status' => 1]);
        }
    }
}
