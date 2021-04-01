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
        switch ($type) {
            case "login":
                $this->trackUserLoginEvent($data);
                break;
            case "search_fcl":
                $this->trackSearchFclEvent($data);
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
     * @return void
     */
    public function trackSearchFclEvent($data)
    {
        $mixPanel = app('mixpanel');

        $user = Auth::user();

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
}
