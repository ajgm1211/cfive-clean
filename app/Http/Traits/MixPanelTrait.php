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

        foreach($containers as $container){
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
}
