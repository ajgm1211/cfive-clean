<?php

namespace App;

use App\Jobs\SyncCompaniesJob;
use App\Partner;
use App\Http\Requests\StoreApiIntegration;
use GuzzleHttp\Client;

class Vforwarding
{
    public function getData($setting)
    {
        try {

            $page = 1;

            do {

                $uri_paginate =  $setting->url . '&k=' . $setting->api_key . '&p=' . $page;

                $response = $this->callApi($uri_paginate);

                $max_page = ceil($response['count'] / 1000);

                SyncCompaniesJob::dispatch($response, \Auth::user(), $setting->partner);
                \Log::info('Running page: ' . $page);

                $page += 1;
            } while ($page <= $max_page);

            return true;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }

    public function callApi($uri)
    {
        $client = new Client([
            'verify' => false,
            'headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']
        ]);

        $response = $client->get($uri);

        $type = $response->getHeader('content-type');

        $type = explode(';', $type[0]);

        $api_response = $response->getBody()->getContents();

        if ($type[1] == 'charset=iso-8859-1') {
            $api_response = iconv("iso-8859-1", "UTF-8", $api_response);
        }

        $data = json_decode($api_response, true);

        return $data;
    }
}
