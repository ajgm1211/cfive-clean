<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ViewQuoteV2;
use App\Company;

class QuotationFilterController extends Controller
{
    private function getBaseQuery() {

        $subtype = auth()->user()->options['subtype'];

        if ($subtype === 'comercial') {
            $query = ViewQuoteV2::filterByCurrentUser();
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

        return $options;
    }

    private function getDestinationOptions($query) {
        return $query->with([
            'destination_harbor' => function ($q) {
                return $q->distinct('id')->get([
                    'harbors.id', 'harbors.display_name'
                ]);
            }
        ])->get([
            'id'
        ])->pluck('destination_harbor')
        ->flatten()
        ->unique('id')->values()
        ->map(function ($harbor) {
            $harbor->label = $harbor->display_name;
            unset($harbor->display_name);
            unset($harbor->quote_id);
            return $harbor;
        });
    }

    private function getOriginOptions($query) {
        return $query->with([
            'origin_harbor' => function ($q) {
                return $q->distinct('id')->get([
                    'harbors.id', 'harbors.display_name'
                ]);
            }
        ])->get([
            'id'
        ])->pluck('origin_harbor')
        ->flatten()
        ->unique('id')->values()
        ->map(function ($harbor) {
            $harbor->label = $harbor->display_name;
            unset($harbor->display_name);
            unset($harbor->quote_id);
            return $harbor;
        });
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
        $companyIds = $query->distinct('company_id')->get(['company_id']);        
        $companies = Company::whereIn('id', $companyIds)->get(['id', 'business_name']);

        return $companies->map(function ($c) {
            $c->label = $c->business_name;
            unset($c->business_name);
            return $c;
        });
    }
    
    private function getCustomQuoteIdOptions($query) {
        return $query->distinct('custom_quote_id')->pluck('custom_quote_id');
    }

    private function getQuoteIdOptions($query) {
        return $query->distinct('quote_id')->pluck('quote_id');
    }

    private function getUserIdOptions($query) {
        return $query->with([
            'user' => function ($q) {
                return $q->select(['id', 'name', 'lastname']);
            }
        ])->distinct('user_id')
        ->get(['user_id'])
        ->pluck('user')
        ->map(function ($u) {
            $u->label = $u->name . ' ' . $u->lastname;
            unset($u->name);
            unset($u->lastname);
            return $u;
        });
    }

}
