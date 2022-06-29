<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Traits\QuoteV2Trait;

class AddDataToCacheForQuoteFilterOptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use QuoteV2Trait;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        try {
            $query = $this->getFilterByUserType($this->$user);
    
            $this->getCacheIdOptions($this->user->company_user_id, $query);
            $this->getCacheQuoteIdOptions($this->user->company_user_id, $query);
            $this->getCacheCustomQuoteIdOptions($this->user->company_user_id, $query);
            $this->getCacheCompaniesOptions($this->user->company_user_id, $query);
            $this->getCacheCreatedAtOptions($this->user->company_user_id, $query);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
