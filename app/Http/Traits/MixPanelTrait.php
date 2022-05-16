<?php

namespace App\Http\Traits;

use App\Carrier;
use Illuminate\Support\Collection as Collection;
use GeneaLabs\LaravelMixpanel\LaravelMixpanel;
use Illuminate\Support\Facades\Auth;
use App\Container;

trait MixPanelTrait
{
    /**
     * trackEvents
     *
     * @param  mixed $type
     * @param  mixed $data
     * @param  mixed $env
     * @return void
     */
    public function trackEvents($type, $data, $env = "web")
    {
        /** Checking if event is from API */
        if ($env == "api") {
            /** Executing events from API */
            $this->executeApiEvent($type, $data);
        }

        /** Executing events from web app */
        $this->executeWebEvent($type, $data);
    }

    /**
     * executeWebEvent
     *
     * @param  mixed $type
     * @param  mixed $data
     * @return void
     */
    public function executeWebEvent($type, $data)
    {
        $user = Auth::user();

        switch ($type) {
            case "login":
                $this->trackUserLoginEvent($data);
                break;
            case "search_fcl":
                $this->trackSearchFclEvent($data, $user);
                break;
            case "create_quote":
                $this->trackCreateQuoteEvent($data, $user);
                break;
            case "status_quote":
                    $this->trackQuoteStatusEvent($data, $user);
                break;    
            case "Request_Status_fcl":
                $this->trackStatusFclEvent($data, $user);
                break;
            case "Request_Review":
                $this->trackReviewEvent($data, $user);
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
            case "old_create_quote":
                $this->trackOldCreateQuoteEvent($data, $user);
                break;
            case "new_request_Fcl":
                $this->trackNewRequestFclEvent($data, $user);
                break;
            case "new_request_by_carrier":
                $this->trackNewRequestByCarrierEvent($data, $user);
                break;
            case "new_request_Lcl":
                $this->trackNewRequestLclEvent($data, $user);
                break;
            case "new_request_carrier_lcl":
                $this->trackNewRequestLclEvent($data, $user);
                break;
            case "search_lcl":
                $this->trackSearchLclEvent($data, $user);
                break;
        }
    }

    /**
     * executeApiEvent
     *
     * @param  mixed $type
     * @param  mixed $data
     * @return void
     */
    public function executeApiEvent($type, $data)
    {
        $user = Auth::user();

        switch ($type) {
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
            case "api_contacts_list":
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

        foreach ($data['data']['originPorts'] as $orig) {
            $origin[] = $orig['display_name'];
        }
        foreach ($data['data']['destinationPorts'] as $dest) {
            $destiny[] = $dest['display_name'];
        }
        if (!empty($origin) &&  !empty($destiny)) {
            $mixPanel->track(
                'Rate Finder FCL',
                array(
                    'Company' => $data['company_user']['name'],
                    'Origin' => $origin,
                    'Destination' => $destiny,
                    'Container_type' => $data['data']['selectedContainerGroup']['name'],
                    'User' => $user->fullname,
                )
            );
        } else {
            \Log::error('The origin port or destination port is empty , the user is ' . $user->email);
        }
    }

    /**
     * trackCreateQuoteEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackCreateQuoteEvent($data, $user)
    {
        $container_arr = [];

        if($data->type == "FCL"){
            $containers = $data->getContainersFromEquipment($data->equipment);
        
            foreach ($containers as $container) {
                array_push($container_arr, $container->code);
            }
        }

        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'Create Quote',
            array(
                'Company' => $data->company_user->name,
                'Type' => $data->type,
                'Status' =>$data->status,
                'Equipment' => $container_arr,
                'Delivery_type' => $data->delivery,
                'Client_company' => $data->company->business_name ?? null,
                'Client_contact' => $data->contact->fullname ?? null,
                'User' => $user->fullname,
            )
        );
    }


    public function trackQuoteStatusEvent($data, $user)
    {
        $container_arr = [];

        if($data->type == "FCL"){
            $containers = $data->getContainersFromEquipment($data->equipment);
        
            foreach ($containers as $container) {
                array_push($container_arr, $container->code);
            }
        }

        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'Status Quote',
            array(
                'Company' => $data->company_user->name,
                'Number Quote' => $data->quote_id,
                'Status' =>$data->status,
                'Type' => $data->type,
                'Equipment' => $container_arr,
                'Delivery_type' => $data->delivery,
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

    /**
     * trackStatusFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackStatusFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);
        $date = explode("/", $data->validation);

        if($data->status == 'Done'){
            $event = 'Request Done FCL';
        }else{
            $event = 'Request FCL Status Updates';
        }
        $mixPanel->track(
            $event,
            array(
                'Company'       => $data->companyuser->name,
                'User'          => $user->fullname,
                'Contract'      => $data->namecontract,
                'Valid_from'    => $date[0],
                'Valid_until'   => $date[1],
                'Status'        => $data->status,
                'Owner'         => $user->username_load,
                'Created_at'    => $data->created_at->format('Y-m-d'),
                'Type'          => $data->module,
                'App'           => 'Cargofive'
            )
        );
    }
    
    /**
     * trackReviewEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    
    public function trackReviewEvent($data, $user)
    {
        $mixPanel = app('mixpanel');
        $mixPanel->identify($user->id);
        $date = explode("/", $data->validation);
        $event = 'Request Review';
        $parameters = array(
                'Company'       => $data->companyuser->name,
                'User'          => $user->fullname,
                'Contract'      => $data->namecontract,
                'Valid_from'    => $date[0],
                'Valid_until'   => $date[1],
                'Status'        => $data->status,
                'Owner'         => $data->username_load,
                'Created_at'    => $data->created_at->format('Y-m-d'),
                'Average'       => $data->time_total,
                'Type'          => $data->module,
                'Created_at'    => $data->created_at->format('Y-m-d'),
                'App'           => 'Cargofive'
            );
        $mixPanel->track($event,$parameters);
        $parameters['Request_id'] = $data->id;
        $parameters['App'] = ($data->manage_app == 'others') ? 'Cargofive':'Barracuda';
        $this->trackRequestByAppEvent($parameters,$user);
    }
    
    /**
     * trackRequestByAppEvent
     *
     * @param  mixed $parameters
     * @param  mixed $user
     * @return void
     */
    
    public function trackRequestByAppEvent($parameters,$user)
    {
        $mixPanel = app('mixpanel');
        $mixPanel->identify($user->id);
        $data = $parameters;
        unset($data['Average']);
        $mixPanel->track('Request By App',$data);
    }

    /**
     * trackStatusLclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackStatusLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);
        $date = explode("/", $data->validation);

        if($data->status == 'Done'){
            $event = 'Request Done LCL';
        }else{
            $event = 'Request LCL Status Updates';
        }

        $mixPanel->track(
            $event,
            array(
                'Company'       => $data->companyuser->name,
                'Status'        => $data->status,
                'User'          => $user->fullname,
                'Contract'      => $data->namecontract,
                'Valid_from'    => $date[0],
                'Valid_until'   => $date[1],
                'Owner'         => $data->username_load,
                'Created_at'    => $data->created_at->format('Y-m-d'),
                'Type'          => 'LCL',
                'App'           => 'Cargofive'
            )
        );
    }

    /**
     * trackOldSearchFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackOldSearchFclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $equipment = array();
        foreach ($data['equipment'] as $equipment_id) {
            if ($data['contain'][$equipment_id] != null) {
                $equipment[] = $data['contain'][$equipment_id];
            }
        }
        foreach ($data['origin'] as $q) {
            $origin[] = $data['harbors'][$q];
        }
        foreach ($data['destiny'] as $q) {
            $destiny[] = $data['harbors'][$q];
        }

        $mixPanel->track(
            'Old Search FCL',
            array(
                'type' => 'FCL',
                'Company' => $data['company'],
                'Client_company' => $data['company_client'] ?? null,
                'Client_contact' => $data['contact_client'] ?? null,
                'Container_group' => $data['type_container'],
                'origin' => $origin,
                'destiny' => $destiny,
                'Container_type' => $equipment,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackOldSearchLclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackOldSearchLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        foreach ($data['origin'] as $q) {
            $origin[] = $data['harbors'][$q];
        }
        foreach ($data['destiny'] as $q) {
            $destiny[] = $data['harbors'][$q];
        }

        $mixPanel->track(
            'Old Search LCL',
            array(
                'type' => 'LCL',
                'Company' => $data['company'],
                'Client_company' => $data['company_client'] ?? null,
                'Client_contact' => $data['contact_client'] ?? null,
                'origin' => $origin,
                'destiny' => $destiny,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackOldCreateQuoteEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackOldCreateQuoteEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $array = [];
        $containers = Container::select('id', 'code')->get();

        foreach (json_decode($data->equipment) as $val) {
            foreach ($containers as $cont) {
                if ($val == $cont->id) {
                    $array[] = $cont->code;
                    //array_push($array, $cont->code);
                }
            }
        }

        $mixPanel->track(
            'Old Create Quote',
            array(
                'Type' => $data->type,
                'Company' => $data->company_user->name,
                'Client_company' => $data->company->business_name ?? null,
                'Client_contact' => $data->contact->fullname ?? null,
                'Container_type' => $array ?? null,
                'User' => $user->fullname,
            )
        );
    }

    /**
     * trackRequestFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackNewRequestFclEvent($data, $user)
    {

        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $container = json_decode($data->data);
        $mixPanel->track(
            'New Request FCL',
            array(
                'Type' => 'FCL',
                'Company' => $user->companyUser->name,
                'Contract_id' => $data->contract_id,
                'Container_type' => $container->group_containers->name,
                'User' => $user->fullname,
                'Created_at' => $data->created_at->format('Y-m-d'),
                'Extension' => $data->file_ext,
                'App' => 'Cargofive'
            )
        );
    }

    /**
     * trackRequestLclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackNewRequestLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $mixPanel->track(
            'New Request LCL',
            array(
                'Type' => 'LCL',
                'Company' => $user->companyUser->name,
                'Contract_id' => $data->contract_id,
                'User' => $user->fullname,
                'Created_at' => $data->created_at->format('Y-m-d'),
                'Extension' => $data->file_ext,
                'App' => 'Cargofive'
            )
        );
    }

    /**
     * trackRequestCarrierFclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackNewRequestByCarrierEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        $carrier = Carrier::find($data->carrier);
        $container = json_decode($data->data);
        $data_container = null;
        if(!empty($container->group_containers)){
            $data_container = $container->group_containers->name;
        }
        $mixPanel->track(
            'New Request By Carrier',
            array(
                'Type' => $data->type,
                'Company' => $user->companyUser->name,
                'Contract_id' => $data->contract_id,
                'Container_type' => $data_container,
                'Carrier' => $carrier->name,
                'User' => $user->fullname,
                'Created_at' => $data->created_at->format('Y-m-d'),
                'App' => 'Cargofive'
            )
        );
    }

    /**
     * trackSearchLclEvent
     *
     * @param  mixed $data
     * @param  mixed $user
     * @return void
     */
    public function trackSearchLclEvent($data, $user)
    {
        $mixPanel = app('mixpanel');

        $mixPanel->identify($user->id);

        foreach ($data['data']['originPorts'] as $orig) {
            $origin[] = $orig['display_name'];
        }
        foreach ($data['data']['destinationPorts'] as $dest) {
            $destiny[] = $dest['display_name'];
        }
        if (!empty($origin) &&  !empty($destiny)) {
            $mixPanel->track(
                'Rate Finder LCL',
                array(
                    'Type'           =>$data['data']['type'],
                    'Company'        => $data['company_user']['name'],
                    'Origin'         => $origin,
                    'Destination'    => $destiny,
                    'Client_company' => $data['data']['company']['business_name'] ?? null,
                    'Client_contact' => ['data']['contact']['name'] ?? null,
                    'Container_type' => $data['data']['cargoType']['name'],
                    'User'           => $user->fullname,
                )
            );
        } else {
            \Log::error('The origin port or destination port is empty , the user is ' . $user->email);
        }
    }
}
