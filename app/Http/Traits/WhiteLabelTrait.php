<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use App\SettingsWhitelabel;
use Illuminate\Support\Collection as Collection;

trait WhiteLabelTrait
{
    public function transferEntityToWhiteLabel($entity, $path){
        $company_user_id = \Auth::user()->company_user_id;
        $route = SettingsWhitelabel::where('company_user_id', $company_user_id)->select('url','token')->first()->toArray();
        $service = new Client();
        $finalRoute = $route['url'].$path;
        $result =   $service->post($finalRoute,
                    [
                        'http_errors' => false,
                        'headers'=>[
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer '.$route['token']
                        ],
                        'json'=>$entity
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
