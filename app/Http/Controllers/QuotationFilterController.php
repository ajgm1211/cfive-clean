<?php

namespace App\Http\Controllers;

use App\CompanyUser;
use App\ViewQuoteV2;

class QuotationFilterController extends Controller
{
    private function getBaseQuery()
    {

        $user = auth()->user();
        $company_user = CompanyUser::where('id', '=', $user->company_user_id)->first();
        $filter_delegation = $company_user['options']['filter_delegations'];
        $subtype = $user->options['subtype'];

        if ($subtype === 'comercial') {
            $query = ViewQuoteV2::filterByCurrentUser();
        } else if ($filter_delegation == true && $user->type == "subuser") {
            $query = ViewQuoteV2::filterByDelegation();
        } else {
            $query = ViewQuoteV2::filterByCurrentCompany();
        }

        return $query;
    }

    public function getFilterOptions()
    {
        $user_id = auth()->user()->id;
        $minutes = 60;
        return cache()->remember('filter_options_required_data_to_user_' . $user_id, $minutes, function () {
            return $this->getDataFilterOptions();
        });
    }

    private function getDataFilterOptions()
    {

        $user = auth()->user();
        $query = $this->getFilterByUserType($user);

        $options = [];

        $options['id'] = $this->getIdOptions($query);
        $options['quote_id'] = $this->getQuoteIdOptions($query);
        $options['custom_quote_id'] = $this->getCustomQuoteIdOptions($query);
        $options['status'] = $this->getStatusOptions($query);
        $options['company_id'] = $this->getCompanyIdOptions($query);
        $options['type'] = $this->getTypeOptions($query);
        $options['origin'] = $this->getHarborsAll();
        $options['destiny'] = $this->getHarborsAll();
        $options['user_id'] = $this->getUserIdOptions($query);
        $options['created_at'] = $this->getCreatedAtOptions($query);

        return $options;
    }

    private function getHarborsAll()
    {
        return Harbor::get(['id', 'display_name'])->map(function ($harbor) {
            $harbor->label = $harbor->display_name;
            unset($harbor->display_name);
            return $harbor;
        });
    }

    private function getIdOptions($query)
    {
        return $query->distinct('id')->pluck('id');
    }

    private function getTypeOptions($query)
    {
        return $query->distinct('type')->pluck('type');
    }

    private function getStatusOptions($query)
    {
        return $query->distinct('status')->pluck('status');
    }

    private function getCompanyIdOptions($query)
    {
        return $query->distinct('company_array')->pluck('company_array');
    }

    private function getCustomQuoteIdOptions($query)
    {
        return $query->distinct('custom_quote_id')->pluck('custom_quote_id');
    }

    private function getQuoteIdOptions($query)
    {
        return $query->distinct('quote_id')->pluck('quote_id');
    }

    private function getUserIdOptions($query)
    {
        return $query->distinct('user_array')->pluck('user_array');
    }

    private function getCreatedAtOptions($query)
    {
        return $query->distinct('created_at')->pluck('created_at')->map(function ($date) {
            return date('Y-m-d', strtotime($date));
        })->unique()->values();
    }

}
