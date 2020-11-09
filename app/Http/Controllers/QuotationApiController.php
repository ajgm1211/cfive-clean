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

        $quotes = QuoteV2::ConditionalWhen($type, $status, $integration)->AuthUserCompany($company_user_id)->get();

        //Update Integration Quote Status
        if ($integration) {
            $this->updateIntegrationStatus($quotes);
        }

        return QuotationApiResource::collection($quotes);
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
        
        return $data;
    }

    public function updateIntegrationStatus($quotes)
    {
        foreach ($quotes as $quote) {
            IntegrationQuoteStatus::where('quote_id', $quote->id)->update(['status' => 1]);
        }
    }
}
