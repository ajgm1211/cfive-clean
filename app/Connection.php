<?php

namespace App;

use App\Jobs\SyncCompaniesJob;
use GuzzleHttp\Client;

class Connection
{

    public function getData($uri)
    {
        $response = $this->callApi($uri);

        return $response;
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
        try {
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

            dump($uri);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return false;
        }

        return $data;

    }
}
