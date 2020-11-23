<?php

namespace App;

use App\AutomaticRate;
use App\Container;
use App\Http\Traits\UtilTrait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\QuoteV2;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LclPdf
{

    use UtilTrait;
    
    public function generate($quote)
    {

        $freight_charges = AutomaticRate::GetCharge(3)->GetQuote($quote->id)->with('charge')->get();

        $inlands = $quote->load('inland');

        $view = \View::make('quote.pdf.index', ['quote' => $quote, 'inlands' => $inlands, 'user' => \Auth::user(), 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'equipmentHides' => $equipmentHides, 'containers' => $containers, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges]);

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view)->save('pdf/temp_' . $quote->id . '.pdf');

        return $pdf->stream('quote-' . $quote->id . '.pdf');
    }

}
