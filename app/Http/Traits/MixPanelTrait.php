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
            case "Request_Status_fcl":
                $this->trackStatusFclEvent($data, $user);
                break;
            case "Request_Status_lcl":
                $this->trackStatusLclEvent($data, $user);
                break;
            case "old_search_Fcl":
                $this->trackOldSearchFclEvent($data, $user);
                break;
            case "old_search_lcl":
                $this->trackOldSearchLclEvent($data, $user);
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

    public function trackStatusFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);
        $date=explode("/",$data->validation);
        $mixPanel->track(
            'Request Done FCL',
            array(
                'Company'       => $data->company_user->name,
                'User'          => $user->fullname,
                'namecontract'  => $data->namecontract,
                'validity_from' => $date[0],
                'validity_until'=> $date[1],
                'username_load' => $data->username_load,
            )
        );
    }

    public function trackStatusLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);
        $date=explode("/",$data->validation);
        $mixPanel->track(
            'Request Done LCL',
            array(
                'Company'       => $data->company_user->name,
                'User'          => $user->fullname,
                'namecontract'  => $data->namecontract,
                'validity_from' => $date[0],
                'validity_until'=> $date[1],
                'username_load' => $data->username_load,
            )
        );
    }

    public function trackOldSearchFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);


        $equipment=array();
        foreach ($data['equipment'] as $equipment_id){
            if($data['contain'][$equipment_id]!=null){
                $equipment[]='C'.$data['contain'][$equipment_id];             
            }  
        }
        $mixPanel->track(
            'Old search FCL',
            array(
                'type'=>'FCL',
                'Company' => $data['company'],
                'company_client' => $data['company_client'] ?? null,
                'contact_client' => $data['contact_client'] ?? null,
                'type_container' => $data['type_container'],
                'equipment' => $equipment,
                'User' => $user->fullname,
            )
        );
    }

    public function trackOldSearchLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'Old search LCL',
            array(
                'type'=>'LCL',
                'Company' => $data['company'],
                'company_client' => $data['company_client'] ?? null,
                'contact_client' => $data['contact_client'] ?? null,
                'User' => $user->fullname,
            )
        );
    }
}
