<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\CompanyUser;
use App\Currency;
use App\QuoteV2;
use App\User;
use App\FclPdf;
use App\LclPdf;
use EventIntercom;
use App\Http\Traits\QuoteV2Trait;
use App\IntegrationQuoteStatus;

class UpdatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, QuoteV2Trait;
    protected $id;
    protected $company_user_id;
    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $company_user_id, $user_id)
    {
        $this->id = $id;
        $this->company_user_id = $company_user_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {    
        $company_user=null;
        $currency_cfg =null;
        $quotes = IntegrationQuoteStatus::where('status',0)->get();
       
        foreach($quotes as $item){

            $quote = $item->quote();
            $quote->clearMediaCollection('document');
            
            switch ($quote->type) {
                case "FCL":
                    $pdf = new FclPdf();
                    return $pdf->generate($quote);
                    // EVENTO INTERCOM
                    $event = new EventIntercom();
                    $event->event_pdfFcl();
                    break;
                case "LCL":
                    $pdf = new LclPdf();
                    return $pdf->generate($quote);
                    // EVENTO INTERCOM
                    $event = new EventIntercom();
                    $event->event_pdfLcl();
                    break;
                }

                \Log::info('Generando pdf');
                
        ProcessPdfApi::dispatch($quote)->onQueue('default');
        // $quote = QuoteV2::find($this->id);
        //  
        // if($this->company_user_id){
        //     $company_user=CompanyUser::find($this->company_user_id);
        //     $currency_cfg = Currency::find($company_user->currency_id);
        // }
        // $pdfarray= $this->generatepdf($quote->id,$company_user,$currency_cfg,$this->user_id);
        // $pdf = $pdfarray['pdf'];
        // $view = $pdfarray['view'];
        // $idQuote= $pdfarray['idQuote'];
        // $idQ = $pdfarray['idQ'];
        // $pdf->loadHTML($view)->save(public_path().'/pdf/quote-'.$idQuote.'.pdf');
        }
      
    }
}
