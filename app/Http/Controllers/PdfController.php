<?php

namespace App\Http\Controllers;

use App\QuoteV2;
use App\FclPdf;
use App\LclPdf;

class PdfController extends Controller
{
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
