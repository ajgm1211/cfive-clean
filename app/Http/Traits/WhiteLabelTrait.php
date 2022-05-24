<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use App\SettingsWhitelabel;
use Illuminate\Support\Collection as Collection;

trait WhiteLabelTrait
{
    public function callApiTransferCompanyToWhiteLabel($companies){
        $company_user_id = \Auth::user()->company_user_id;
        $url = SettingsWhitelabel::where('company_user_id', $company_user_id)->select('url','token')->first()->toArray();  
        $endPoint = $url['url'].'shipper';
        $service = new Client();
        
        $result =   $service->post($endPoint,
                    [
                        'http_errors' => false,
                        'headers'=>[
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer '.$url['token']
                        ],
                        'json'=>$companies
                    ]
                );

        $resultBody = $result->getBody()->getContents();
        $resultStatus = $result->getStatusCode();
        return [
            'status'=> $resultStatus, 
            'body'  => $resultBody
        ];
    }
}
