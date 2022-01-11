<?php

namespace App\Http\Controllers;

use App\AutoImportation;
use App\Duplicados;
use App\Harbor;
use App\Jobs\SendEmailAutoImporJob;
use App\Jobs\SendEmailRequestFclJob;
use App\Jobs\TestJob;
use App\NewContractRequest;
use App\User;
use App\Country;
use App\Surcharge;
use Goutte\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Intercom\IntercomClient;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $url = 'https://www.maersk.com/webuser-rest-war/loginwithusernamepassword';
        $array = [
            'body' => 'userName=juanfrag&password=Gencomex18%24&skipCustCodeSelect=N&timestamp=1567673525531',
        ];

        $response = $client->request('POST', $url, [
            'body' => 'userName=juanfrag&password=Gencomex18%24&skipCustCodeSelect=N&timestamp=1567673525531',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]); /*
        $client = new \GuzzleHttp\Client(["base_uri" => "https://www.maersk.com"]);
        $response = $client->post("/webuser-rest-war/loginwithusernamepassword");*/
        $body = $response->getBody();
        dd($body);
        //return $json = PrvUserConfigurations::allData(1);
        //return view('testings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendJob($user_id, $request)
    {
        SendEmailRequestFclJob::dispatch($user_id, $id);
    }

    public function create(Request $request)
    {
        $client = new Client(['cookies' => true]);
        $path = '/var/www/html/cargofive/storage/app/public/cookies.json';
        $cookFi = new FileCookieJar($path, true);
        //$cookFi->save($path);
        $crawler = $client->request('GET', 'https://www.cma-cgm.com/ebusiness/my-prices/GetQuoteLines/0005926016/ST/2019-09-20/CNSHA/ARBUE', ['cookies' => $cookFi]);

        //dd($wa.'\n'.$wresult.'\n'.$wctx);
        $crawler = $client->getResponse()->getContent();
        dd($crawler);

        /*SelectionAutoImportJob::dispatch($request->text1,'fcl');
    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.content', 'OK');

    return back();*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $req_id = $id;
        $request_cont = NewContractRequest::find($req_id);
        $request_cont->load('Requestcarriers');
        $user_adm_rq = User::where('email', 'admin@example.com')->orWhere('email', 'info@cargofive.com')->first();
        $admins = User::where('type', 'admin')->get();
        if (count($request_cont->Requestcarriers) == 1) {
            $autoImp = AutoImportation::whereHas('carriersAutoImportation', function ($query) use ($request_cont) {
                $query->whereIn('carrier_id', $request_cont->Requestcarriers->pluck('carrier_id'));
            })->where('status', 1)->first();
            if (!empty($autoImp)) {
                try {
                    if (env('APP_ENV') == 'local') {
                        $client = new Client(['base_uri' => 'http://contractsai/']);
                    } elseif (env('APP_ENV') == 'developer') {
                        $client = new Client(['base_uri' => 'http://dev.contractsai.cargofive.com/']);
                    } else {
                        $client = new Client(['base_uri' => 'http://prod.contractsai.cargofive.com/']);
                    }
                    //$response = $client->get('login?email=admin@example.com&password=secret');
                    //$response = $client->request('GET','ConverterFile/CFIndex', [
                    $response = $client->request('GET', 'ConverterFile/CFDispatchJob/' . $req_id, [
                        'headers' => [
                            //'Authorization' => $auth->api_key,
                            'Authorization' => 'Bearer ' . $user_adm_rq->api_token,
                            'Accept' => 'application/json',
                        ],
                    ]);
                    $dataGen = json_decode($response->getBody()->getContents(), true);
                    dd($dataGen);
                    //return $dataGen;
                } catch (RequestException $e) {
                    //Enviar correo falla de conexion
                    $message = 'connection failure, Request Id: ' . $req_id . ' I qualify for Auto-Import ENV: ' . env('APP_ENV');
                    dd($message);
                    foreach ($admins as $userNotifique) {
                        SendEmailAutoImporJob::dispatch($userNotifique->email, $message);
                    }
                }
            }
        } else {
            $autoImp = AutoImportation::whereHas('carriersAutoImportation', function ($query) use ($request_cont) {
                $query->whereIn('carrier_id', $request_cont->Requestcarriers->pluck('carrier_id'));
            })->where('status', 1)->first();
            if (!empty($autoImp)) {
                //Enviar correo
                $message = 'There is more than one carrier and one of them are listed in the Auto Import. Request Id: ' . $req_id;
                dd($message);
                foreach ($admins as $userNotifique) {
                    SendEmailAutoImporJob::dispatch($userNotifique->email, $message);
                }
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        //dd($request);

        TestJob::dispatch();
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'OK');

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createIntercom()
    {

        /*$user = User::find(1);
        dd($user);*/
        $client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.4']);
        /*  \DB::table('users')->chunkById(100, function ($users) use($client) {
        foreach ($users as $user) {

        $this->intercom($client, $user);
        }
        });*/

        $user = User::where('email', 'araceli@acrosslogistics.com')->first();
        $this->intercom($client, $user);
        echo "Finalizado";

        echo 'Finalizado';
    }

    public function intercom($client, $user)
    {
        try {
            $cliente = $client->users->getUsers(['email' => $user->email]);
        } catch (Exception $e) {
            echo $user->email;
        }
        dd($cliente->total_count);
        if ($cliente->total_count > 1) {
            echo "Mas de uno " . $user->email . "<BR>";
            foreach ($cliente->users as $u) {
                if ($u->type == 'user') {
                    if ($u->user_id != $user->id) {

                        //$client->users->archiveUser($u->id);
                        echo "Diferente id " . $user->email . "<BR>";
                    }
                }
            }
        }

        /*if ($cliente->total_count == 0) {

    if ($user->company_user_id != "") {
    //setHashID();

    $client->users->create([
    'email' => $user->email,
    'companies' => [
    [
    'name' => $user->companyUser->name,
    'company_id' => $user->company_user_id,
    ],
    ],
    ]);
    } else {
    $client->users->create([
    'email' => $user->email,
    'user_id' => $user->id,
    'name' => $user->name,
    ]);
    }

    }*/
    }

    public function intercom2($client, $user)
    {
        try {
            $cliente = $client->users->getUsers(['email' => $user->email]);
        } catch (Exception $e) {
            echo $user->email;
        }
        dd($cliente->total_count);
        if ($cliente->total_count > 1) {
            echo "Mas de uno " . $user->email . "<BR>";
            foreach ($cliente->users as $u) {
                if ($u->type == 'user') {
                    if ($u->user_id != $user->id) {

                        //$client->users->archiveUser($u->id);
                        echo "Diferente id " . $user->email . "<BR>";
                    }
                }
            }
        }

    }

    public function contable(Request $request)
    {

      
        $info = Country::find(2);
        $ports = $info->ports->pluck('id')->toArray();
        return $ports;
    }

}
