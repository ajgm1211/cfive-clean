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
            $myBody['client_id'] = \Config::get('values.client_id');
            $myBody['client_secret'] = \Config::get('values.client_secret');
            $myBody['grant_type'] = \Config::get('values.grant_type');
            $myBody['username'] = \Config::get('values.username');
            $myBody['password'] = \Config::get('values.password');
            $res = $client->request('POST', $url, ['form_params'=>$myBody])->getBody()->getContents();
        }catch (\Guzzle\Http\Exception\ConnectException $e) {
            return json_decode($e);
        }
        return json_decode($res);
    }

    public function getSchedules($token,$carrier,$origin,$destination,$date){
        try{
            $client = new Client();

            $get_url = "http://smanual-ec2.eu-central-1.elasticbeanstalk.com/api/".$carrier."/".$origin."/".$destination."/".$date;

            $get_response = $client->request('GET', $get_url, [

                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],

            ]);
        }catch (\Guzzle\Http\Exception\ConnectException $e) {
            return json_decode($e);
        }
        return json_decode($get_response->getBody());
    }
}