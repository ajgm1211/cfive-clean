<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11-02-19
 * Time: 18:38
 */

namespace App\Repositories;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class Schedules
{
    public function authentication(){
        try{
            $client = new Client();

            $url = "http://smanual-ec2.eu-central-1.elasticbeanstalk.com/oauth/token";

            $myBody['client_id'] = "1";
            $myBody['client_secret'] = "MrGWuViDv1r8LI8ETzceRHTfpC48Nn7hm4GeAIBA";
            $myBody['grant_type'] = "password";
            $myBody['username'] = "info@cargofive.com";
            $myBody['password'] = "secret";
            $res = $client->request('POST', $url, ['form_params'=>$myBody])->getBody()->getContents();
        }catch (\Guzzle\Http\Exception\ConnectException $e) {

        }
        return json_decode($res);
    }

    public function getSchedules($token,$carrier,$origin,$destination){
        try{
        $client = new Client();

        $get_url = "http://smanual-ec2.eu-central-1.elasticbeanstalk.com/api/".$carrier."/".$origin."/".$destination;

        $get_response = $client->request('GET', $get_url, [

            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],

        ]);
        }catch (\Guzzle\Http\Exception\ConnectException $e) {

        }
        return json_decode($get_response->getBody()->getContents());
    }
}