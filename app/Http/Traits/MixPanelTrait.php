<?php

namespace App\Http\Traits;

use Illuminate\Support\Collection as Collection;
use GeneaLabs\LaravelMixpanel\LaravelMixpanel;
use Illuminate\Support\Facades\Auth;

trait MixPanelTrait
{
    /**
     * trackEvents
     *
     * @param  mixed $type
     * @param  mixed $data
     * @return void
     */
    public function trackEvents($type, $data)
    {
        $user = Auth::user();

        switch ($type) {
            case "login":
                $this->trackUserLoginEvent($data);
                break;
            case "search_fcl":
                $this->trackSearchFclEvent($data, $user);
                break;
            case "create_quote_fcl":
                $this->trackCreateQuoteEvent($data, $user);
                break;
            case "api_rate_fcl":
                $this->trackApiRateFclEvent($data, $user);
                break;
            case "api_quotes_v2":
                $this->trackApiQuoteV2Event($user);
                break;
            case "api_quotes_v2_by_id":
                $this->trackApiQuoteV2ByIdEvent($user);
                break;
            case "api_companies_list":
                $this->trackApiCompaniesListEvent($user);
                break;
            case "api_conctacts_list":
                $this->trackApiContactsListEvent($user);
                break;
        }
    }

    /**
     * trackUserLoginEvent
     *
     * @param  mixed $user
     * @return void
     */
    public function trackUserLoginEvent($user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->people->set($user->id, array(
            '$name'      => $user->name,
            '$lastname'  => $user->lastname,
            '$phone'     => $user->phone,
            '$company'   => $user->companyUser->name,
        ));
    }

    /**
     * trackSearchFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackSearchFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'Rate Finder FCL',
            array(
                'Company' => $data['company_user']['name'],
                'Container_type' => $data['data']['selectedContainerGroup']['name'],
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackCreateQuoteFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackCreateQuoteEvent($data, $user)
    {
        $containers = $data->getContainersFromEquipment($data->equipment);

        $container_arr = [];

        foreach ($containers as $container) {
            array_push($container_arr, $container->code);
        }

        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'Create Quote',
            array(
                'Company' => $data->company_user->name,
                'Type' => $data->type,
                'Equipment' => $container_arr,
                'Delivery_type' => $data->delivery,
                'Client_company' => $data->company->business_name ?? null,
                'Client_contact' => $data->contact->fullname ?? null,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackApiRateFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackApiRateFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'API Rates FCL',
            array(
                'Company' => $user->companyUser->name,
                'Origin' => $data['origin'],
                'Destination' => $data['destination'],
                'From' => $data['from'],
                'Until' => $data['until'],
                'Container_group' => $data['group'],
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackApiQuoteV2Event
     *
     * @param  mixed $user
     * @return void
     */
    public function trackApiQuoteV2Event($user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'API Quotes V2 List',
            array(
                'Company' => $user->companyUser->name,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackApiQuoteV2ByIdEvent
     *
     * @param  mixed $user
     * @return void
     */
    public function trackApiQuoteV2ByIdEvent($user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'API Quotes V2 By ID',
            array(
                'Company' => $user->companyUser->name,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackApiCompaniesListEvent
     *
     * @param  mixed $user
     * @return void
     */
    public function trackApiCompaniesListEvent($user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'API Companies List',
            array(
                'Company' => $user->companyUser->name,
                'User' => $user->fullname,
            )
        );
    }
    
    /**
     * trackApiContactsListEvent
     *
     * @param  mixed $user
     * @return void
     */
    public function trackApiContactsListEvent($user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'API Contacts List',
            array(
                'Company' => $user->companyUser->name,
                'User' => $user->fullname,
            )
        );
    }
}
