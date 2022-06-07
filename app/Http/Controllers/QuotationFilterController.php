<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ViewQuoteV2;
use App\Company;
use App\Harbor;
use App\CompanyUser;

class QuotationFilterController extends Controller
{
    private function getBaseQuery() {
        
        $user = auth()->user();
        $company_user = CompanyUser::where('id','=',$user->company_user_id)->first();
        $filter_delegation = $company_user['options']['filter_delegations'];
        $subtype = $user->options['subtype'];
        
        if ($subtype === 'comercial') {
            $query = ViewQuoteV2::filterByCurrentUser();
        } else if($filter_delegation == true && $user->type == "subuser") {
            $query =  ViewQuoteV2::filterByDelegation();
        } else {
            $query = ViewQuoteV2::filterByCurrentCompany();
        }

        return $query;
    }

    public function getFilterOptions() {
        $query = $this->getBaseQuery();

        $options = [];

        $options['id'] = $this->getIdOptions($query);
        $options['quote_id'] = $this->getQuoteIdOptions($query);
        $options['custom_quote_id'] = $this->getCustomQuoteIdOptions($query);
        $options['status'] = $this->getStatusOptions($query);
        $options['company_id'] = $this->getCompanyIdOptions($query);
        $options['type'] = $this->getTypeOptions($query);
        $options['origin'] = $this->getOriginOptions($query);
        $options['destiny'] = $this->getDestinationOptions($query);
        $options['user_id'] = $this->getUserIdOptions($query);
        $options['created_at'] = $this->getCreatedAtOptions($query);

        return $options;
    }

    private function getDestinationOptions($query) {
        $multiDimArray = $query->distinct('destination_port_array')->pluck('destination_port_array');
        return collect($multiDimArray)->flatMap(function($ad) {
                return $ad;
            })->unique('id')->values();
    }

    private function getOriginOptions($query) {
        $multiDimArray = $query->distinct('origin_port_array')->pluck('origin_port_array'); 
        return collect($multiDimArray)->flatMap(function($a) {
                return $a;
            })->unique('id')->values();        
    }

    private function getIdOptions($query) {
        return $query->distinct('id')->pluck('id');
    }

    private function getTypeOptions($query) {
        return $query->distinct('type')->pluck('type');
    }
    
    private function getStatusOptions($query) {
        return $query->distinct('status')->pluck('status');
    }

    private function getCompanyIdOptions($query) {
        return $query->distinct('company_array')->pluck('company_array');
    }
    
    private function getCustomQuoteIdOptions($query) {
        return $query->distinct('custom_quote_id')->pluck('custom_quote_id');
    }

    private function getQuoteIdOptions($query) {
        return $query->distinct('quote_id')->pluck('quote_id');
    }

    private function getUserIdOptions($query) {
        return $query->distinct('user_array')->pluck('user_array');
    }
    
    private function getCreatedAtOptions($query) {
        return $query->distinct('created_at')->pluck('created_at')->map(function($date){
            return date('Y-m-d', strtotime($date));
        })->unique()->values();
    }

}
