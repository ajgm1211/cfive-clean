<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\QuoteV2Trait;
use App\ViewQuoteV2;
use App\Company;
use App\Harbor;
use App\User;
use App\CompanyUser;
use App\StatusQuote;

class QuotationFilterController extends Controller
{
    use QuoteV2Trait;
    
    public $user;
    public $company_user_id;

    public function getFilterOptions() {

        $this->user = auth()->user();
        $this->company_user_id = auth()->user()->company_user_id;

        $query = $this->getFilterByUserType($this->user);

        $options = [];

        $options['id'] = $this->getCacheIdOptions($this->company_user_id, $query);
        $options['quote_id'] = $this->getCacheQuoteIdOptions($this->company_user_id, $query);
        $options['custom_quote_id'] = $this->getCacheCustomQuoteIdOptions($this->company_user_id, $query);
        $options['status'] = $this->getCacheStatusOptions();
        $options['company_id'] = $this->getCacheCompaniesOptions($this->company_user_id, $query);
        $options['type'] = $this->getCacheTypeOptions();
        $options['origin'] = $this->getCacheHarborsOptions();
        $options['destiny'] = $this->getCacheHarborsOptions();
        $options['user_id'] = $this->getCacheUsersOptions();
        $options['created_at'] = $this->getCacheCreatedAtOptions($this->company_user_id, $query);

        return $options;
    }

}