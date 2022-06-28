<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ViewQuoteV2;
use App\Company;
use App\Harbor;
use App\User;
use App\CompanyUser;
use App\StatusQuote;

class QuotationFilterController extends Controller
{
    public function getFilterByUserType($user) {

        $filter_delegation = $user->companyUser->options['filter_delegations'];
        $subtype = $user->options['subtype'];

        //Filtro por permisos a nivel de usuario y compañía
        if ($subtype === 'comercial') {
            $query = ViewQuoteV2::filterByCurrentUser();
        }
        if ($filter_delegation == true) {
            $query = ViewQuoteV2::filterByDelegation();
        } else {
            $query = ViewQuoteV2::filterByCurrentCompany();
        }
        return $query;
    }

    public function getFilterOptions() {
        
        $user = auth()->user();
        $query = $this->getFilterByUserType($user);

        $options = [];

        $options['id'] = $this->getIdOptions($query);
        $options['quote_id'] = $this->getQuoteIdOptions($query);
        $options['custom_quote_id'] = $this->getCustomQuoteIdOptions($query);
        $options['status'] = $this->getStatusOptions();
        $options['company_id'] = $this->getCompaniesOptions($query);
        $options['type'] = $this->getTypeOptions();
        $options['origin'] = $this->getHarborsAll();
        $options['destiny'] = $this->getHarborsAll();
        $options['user_id'] = $this->getUsersOptions();
        $options['created_at'] = $this->getCreatedAtOptions($query);

        return $options;
    }

    private function getHarborsAll() {
        return cache()->rememberForever('habors_all', function() {
            return Harbor::get(['id', 'display_name'])->map(function ($harbor) {
                $harbor->label = $harbor->display_name;
                unset($harbor->display_name);
                return $harbor;
            });
        });
    }

    private function getIdOptions($query) {
        $user_id = auth()->user()->id;
        return cache()->rememberForever('id_options_to_quotes_by_user_'.$user_id, function() use ($query) {
           return $query->distinct('id')->pluck('id'); 
        });
    }

    private function getTypeOptions() {
        return cache()->rememberForever('quote_types', function() {
            return ['FCL', 'LCL'];
        });
    }
    
    private function getStatusOptions() {
        return cache()->rememberForever('quote_status', function() {
            return StatusQuote::pluck('name');
        });
    }

    private function getCompaniesOptions($query) {
        $user_id = auth()->user()->id;
        return cache()->rememberForever('companies_option_to_quotes_by_user_'.$user_id, function() use ($query) {
            return $query->distinct('company_array')->pluck('company_array');
        });
    }
    
    private function getCustomQuoteIdOptions($query) {
        $user_id = auth()->user()->id;
        return cache()->rememberForever('custom_quote_id_options_to_quotes_by_user_'.$user_id, function() use ($query) {
            return $query->distinct('custom_quote_id')->pluck('custom_quote_id');
        });
    }

    private function getQuoteIdOptions($query) {
        $user_id = auth()->user()->id;
        return cache()->rememberForever('quote_id_options_to_quotes_by_user_'.$user_id, function() use ($query) {
            return $query->distinct('quote_id')->pluck('quote_id');
        });
    }

    private function getUsersOptions() {
        $company_user_id = auth()->user()->company_user_id;
        return cache()->rememberForever('users_by_company_'.$company_user_id, function() use ($company_user_id) {
            return User::where('company_user_id', $company_user_id)
                ->get(['id', 'name', 'lastname'])
                ->map(function($user) {
                    $user->label = $user->name.' '.$user->lastname;
                    unset($user->name);
                    unset($user->lastname);
                    return $user;
                });
        });
    }
    
    private function getCreatedAtOptions($query) {
        $user_id = auth()->user()->id;
        return cache()->rememberForever('created_at_options_to_quotes_by_user_'.$user_id, function() use($query) {
            return $query->distinct('created_at')->pluck('created_at')->map(function($date){
                return date('Y-m-d', strtotime($date));
            })->unique()->values();
        });
    }

    public function cacheForgetByCurrentUser(){
        $user_id = auth()->user()->id;
        cache()->forget('created_at_options_to_quotes_by_user_'.$user_id);
        cache()->forget('quote_id_options_to_quotes_by_user_'.$user_id);
        cache()->forget('custom_quote_id_options_to_quotes_by_user_'.$user_id);
        cache()->forget('companies_option_to_quotes_by_user_'.$user_id);
        cache()->forget('id_options_to_quotes_by_user_'.$user_id);
    }

}