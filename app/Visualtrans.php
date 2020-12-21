<?php

namespace App;

use App\Jobs\SyncCompaniesJob;
use GuzzleHttp\Client;

class Visualtrans
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

    public function getInvoices($client_id)
    {

        $year = date('Y');

        $response = $this->callApi('https://altius.visualtrans.net/rest/api1-facturas-venta.pro?v=ejercicio%3A' . $year . '%2C%20cliente%3A' . $client_id . '&k=ENTICARGOFIVE75682100');

        if ($response['count'] > 0) {
            return true;
        }

        return false;
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
