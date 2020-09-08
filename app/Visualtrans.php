<?php

namespace App;

use App\Jobs\SyncCompaniesJob;
use App\Partner;
use App\Http\Requests\StoreApiIntegration;
use GuzzleHttp\Client;

class Visualtrans
{
    public function getData($client, $endpoint, $setting)
    {
        try {

            $response = $client->get($endpoint);

            $type = $response->getHeader('content-type');

            $type = explode(';', $type[0]);

            $api_response = $response->getBody()->getContents();

            if ($type[1] == 'charset=iso-8859-1') {
                $api_response = iconv("iso-8859-1", "UTF-8", $api_response);
            }

            $result = json_decode($api_response, true);

            $page = 1;

            $max_page = ceil($result['count'] / 100);

            do {
                $uri_paginate =  $setting->url . $setting->api_key . '&p=' . $page;

                $get = $client->get($uri_paginate);

                $header = $response->getHeader('content-type');

                $header = explode(';', $header[0]);

                $get_response = $get->getBody()->getContents();

                if ($header[1] == 'charset=iso-8859-1') {
                    $get_response = iconv("iso-8859-1", "UTF-8", $get_response);
                }

                $data = json_decode($get_response, true);

                SyncCompaniesJob::dispatch($data, \Auth::user(), $setting->partner);
                \Log::info('Running page: ' . $page);
                
                $page += 1;

            } while ($page <= $max_page);
            
            return true;

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }
}
