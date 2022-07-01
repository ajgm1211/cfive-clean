<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Traits\QuoteV2Trait;
use App\User;

class AddDataToCacheForQuoteFilterOptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use QuoteV2Trait;

    protected $user;
    protected $company_user_id;
    protected $subtype;

    public function __construct($user)
    {   

        $this->user = $user; 
        $this->company_user_id = $user->company_user_id;
        $this->subtype = $user->options['subtype'];
    
    }

    public function handle()
    {   
        try {
            // olvidar cache de la compañia
            cache()->forget('id_options_to_quotes_by_user_'.$company_user_id);
            cache()->forget('quote_id_options_to_quotes_by_user_'.$company_user_id);
            cache()->forget('custom_quote_id_options_to_quotes_by_user_'.$company_user_id);
            cache()->forget('companies_option_to_quotes_by_user_'.$company_user_id);
            cache()->forget('created_at_options_to_quotes_by_user_'.$company_user_id);

            $query = $this->getFilterByUserType($this->user);
            
            $this->getCacheIdOptions($this->company_user_id, $query, $this->subtype);
            $this->getCacheQuoteIdOptions($this->company_user_id, $query, $this->subtype);
            $this->getCacheCustomQuoteIdOptions($this->company_user_id, $query, $this->subtype);
            $this->getCacheCompaniesOptions($this->company_user_id, $query, $this->subtype);
            $this->getCacheCreatedAtOptions($this->company_user_id, $query, $this->subtype);

        } catch (\Exception $e) {
            \Log::error($e->getMessage().$e->getLine());
        }
    }
}
