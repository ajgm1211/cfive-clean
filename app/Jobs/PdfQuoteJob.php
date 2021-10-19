<?php

namespace App\Jobs;

use App\FclPdf;
use App\PdfQuoteStatus;
use App\LclPdf;
use App\QuoteV2;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PdfQuoteJob implements ShouldQueue
{
    use /* Dispatchable,  */InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(/* $id, $company_user_id, $user_id */)
    {
        /* $this->id = $id;
        $this->company_user_id = $company_user_id;
        $this->user_id = $user_id; */
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '3000M');
        $company_user = null;
        $currency_cfg = null;
        $quotes = PdfQuoteStatus::where('status', 0)->get();

        foreach ($quotes as $item) {

            $quote = QuoteV2::find($item->quote_id);
            
            if($quote)
            {
                \Log::info('Quote id: '.$quote->id);
                
                try{
                    $quote->clearMediaCollection('document');
                    switch ($quote->type) {
                        case "FCL":
                            $pdf = new FclPdf();
                            $pdf->generate($quote);
                        case "LCL":
                            $pdf = new LclPdf();
                            $pdf->generate($quote);
                    }

                    \Log::info('Generando pdf');
                    $item->status = 1;
                    $item->save();
                    dispatch(new ProcessPdfApi($quote));
                    
                } catch(\Exception $e) {
                    \Log::info('Error'.$e->getMessage());
                }
            }

        }
    }
}
