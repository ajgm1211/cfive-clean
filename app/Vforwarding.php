<?php

namespace App;

use App\Http\Requests\StoreApiIntegration;
use App\Jobs\SyncCompaniesJob;
use App\Partner;
use GuzzleHttp\Client;

class Vforwarding
{
    public function getData($client, $endpoint, $setting)
    {
        try {
            $response = $client->get($endpoint);

            $type = $response->getHeader('content-type');

            $type = explode(';', $type[0]);

            $api_response = $response->getBody()->getContents();

            if ($type[1] == 'charset=iso-8859-1') {
                $api_response = iconv('iso-8859-1', 'UTF-8', $api_response);
            }

            $result = json_decode($api_response, true);

            SyncCompaniesJob::dispatch($result, \Auth::user(), $setting->partner);

            return true;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());

            return false;
        }
    }
}
