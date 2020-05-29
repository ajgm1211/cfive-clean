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
use App\Http\Traits\QuoteV2Trait;

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
        $quote = QuoteV2::find($this->id);
        $quote->clearMediaCollection('document'); 
        if($this->company_user_id){
            $company_user=CompanyUser::find($this->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }
        $pdfarray= $this->generatepdf($quote->id,$company_user,$currency_cfg,$this->user_id);
        $pdf = $pdfarray['pdf'];
        $view = $pdfarray['view'];
        $idQuote= $pdfarray['idQuote'];
        $idQ = $pdfarray['idQ'];
        $pdf->loadHTML($view)->save(public_path().'/pdf/quote-'.$idQuote.'.pdf');

        ProcessPdfApi::dispatch($quote)->onQueue('default')->delay(now()->addMinutes(1));
    }
}
