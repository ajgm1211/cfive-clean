<?php

namespace App\Http\Controllers;

use App\AutomaticRate;
use App\Container;
use App\Http\Traits\UtilTrait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\QuoteV2;
use App\FclPdf;
use App\LclPdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PdfController extends Controller
{

    use UtilTrait;

    public function quote(QuoteV2 $quote)
    {
        switch ($quote->type) {
            case "FCL":
                $pdf = new FclPdf();
                return $pdf->generate($quote);
                break;
            case "LCL":
                $pdf = new LclPdf();
                return $pdf->generate($quote);
                break;
        }
    }
}