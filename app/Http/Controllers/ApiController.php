<?php

namespace App\Http\Controllers;

use App\Airline;
use App\Airport;
use App\Carrier;
use App\Company;
use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Contract;
use App\ContractLcl;
use App\Country;
use App\Currency;
use App\GlobalCharCountry;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\Harbor;
use App\Http\Resources\SurchargeResource;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use App\IntegrationQuoteStatus;
use App\LocalCharCountry;
use App\LocalCharge;
use App\LocalChargeApi;
use App\LocalCharPort;
use App\OauthAccessToken;
use App\OauthClient;
use App\QuoteV2;
use App\Rate;
use App\Surcharge;
use App\User;
use App\ViewGlobalCharge;
use App\ViewLocalCharges;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    use SearchTraitApi;
    use UtilTrait;

    public function index()
    {
        if (\Auth::user()->type == 'company') {
            $tokens = OauthClient::where('company_user_id', \Auth::user()->company_user_id)->get();
        } elseif (\Auth::user()->type == 'admin') {
            $tokens = OauthClient::all();
        }

        return view('oauth.index', compact('tokens'));
    }

    public function createAccessToken()
    {
        $token = new OauthClient();

        $token->name = 'Password Grant Token '.str_random(5);
        $token->company_user_id = \Auth::user()->company_user_id;
        $token->secret = str_random(40);
        $token->redirect = 'http://localhost';
        $token->personal_access_client = 0;
        $token->password_client = 1;
        $token->revoked = 0;

        $token->save();

        return redirect('/oauth/list');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        $user = new User([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();

        return response()->json([
            'message' => 'Successfully created user!',
        ], 201);
    }

    public function createToken(Request $request, $user_id)
    {
        /*$request->validate([
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
            }*/
        $user = User::find($user_id);
        $tokenResult = $user->createToken($user->name.' '.$user->lastname.' Token');
        $token = $tokenResult->token;
        /*if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }*/
        $token->save();

        return redirect('users/home');
        /*return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
            ]);*/
    }

    /**
     * Login user and create token.
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }

    public function deleteToken(Request $request, $id)
    {
        $token = OauthClient::find($id);
        $token->delete();

        return response()->json([
            'message' => 'Ok',
        ]);
    }

    /**
     * Logout from session.
     * @param Request $request
     * @return JSON
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Show user info.
     * @param Request $request
     * @return JSON
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function test()
    {
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json', 'Accept' => '*/*'],
        ]);
        try {
            $response = $client->post('http://cargofive.dev.com/oauth/token', [
                'form_params' => [
                    'client_id' => 5,
                    // The secret generated when you ran: php artisan passport:install
                    'client_secret' => 'N35d4dJknC6WRoQy63qL1UfvlvQjJezuIoT0PRBY',
                    'grant_type' => 'password',
                    'username' => 'admin@example.com',
                    'password' => 'secret',
                    'scope' => '*',
                ],
            ]);
            // You'd typically save this payload in the session
            $auth = json_decode((string) $response->getBody());
            //dd($auth->access_token);
            $response = $client->get('http://cargofive.dev.com/api/v1/quotes', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Authorization' => 'Bearer '.$auth->access_token,
                ],
            ]);

            $all = json_decode((string) $response->getBody());
            dd(json_encode($all));
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            echo 'Unable to retrieve access token.';
        }
    }

    /**
     * Show FCL Rates list.
     * @param Request $request
     * @return JSON
     */
    public function rates(Request $request)
    {
        $query = Rate::whereHas('contract', function ($q) {
            $q->where('contracts.company_user_id', \Auth::user()->company_user_id);
        })->with('contract');

        if ($request->paginate) {
            $rates = $query->paginate($request->paginate);
        } else {
            $rates = $query->take($request->size)->get();
        }

        $collection = Collection::make($rates);
        $collection->transform(function ($rate) {
            $rate->origin_port = $rate->port_origin->code;
            $rate->destination_port = $rate->port_destiny->code;
            $rate->carrier_code = $rate->carrier->uncode;
            $rate->currency_code = $rate->currency->alphacode;
            $rate->rate_20 = $rate->twuenty;
            $rate->rate_40 = $rate->forty;
            $rate->rate_40_hc = $rate->fortyhc;
            $rate->rate_40_nor = $rate->fortynor;
            $rate->rate_45 = $rate->fortyfive;
            $rate->valid_from = $rate->contract->validity;
            $rate->valid_until = $rate->contract->expire;
            $rate->contract_name = $rate->contract->name;
            $rate->contract_id = $rate->contract->id;
            unset($rate['port_origin']);
            unset($rate['destiny_port']);
            unset($rate['port_destiny']);
            unset($rate['contract']);
            unset($rate['twuenty']);
            unset($rate['forty']);
            unset($rate['fortyhc']);
            unset($rate['fortynor']);
            unset($rate['fortyfive']);
            unset($rate['created_at']);
            unset($rate['updated_at']);
            unset($rate['deleted_at']);
            unset($rate['carrier_id']);
            unset($rate['currency_id']);
            unset($rate['currency']);
            unset($rate['carrier']);
        });

        return $rates;
    }

    /**
     * Show charges list.
     * @param Request $request
     * @return JSON
     */
    public function charges(Request $request)
    {
        $query = ViewLocalCharges::whereHas('contract', function ($q) {
            $q->where('company_user_id', \Auth::user()->company_user_id);
        })->select(
            'id',
            'contract_id',
            'surcharge',
            'port_orig as origin_port',
            'port_dest as destination_port',
            'country_orig as origin_country',
            'country_dest as destination_country',
            'changetype as charge_type',
            'carrier',
            'calculation_type',
            'currency',
            'ammount as amount'
        )->with('contract');

        if ($request->paginate) {
            $charges = $query->paginate($request->paginate);
        } else {
            $charges = $query->take($request->size)->get();
        }

        return $charges;
    }

    /**
     * Show globalcharge list.
     * @param Request $request
     * @return JSON
     */
    public function globalCharges(Request $request)
    {
        $charges = ViewGlobalCharge::where('company_user_id', \Auth::user()->company_user_id)->take($request->size)->get();

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            if ($charge->origin_port == null) {
                unset($charge['origin_port']);
            }
            if ($charge->destination_port == null) {
                unset($charge['destination_port']);
            }
            if ($charge->origin_country == null) {
                unset($charge['origin_country']);
            }
            if ($charge->destination_country == null) {
                unset($charge['destination_country']);
            }
            unset($charge['carrier']);
            unset($charge['company_user_id']);
        });

        return $charges;
    }

    /**
     * Show contracts list.
     * @param Request $request
     * @return JSON
     */
    public function contracts(Request $request)
    {
        $query = Contract::where('company_user_id', '=', Auth::user()->company_user_id);

        if ($request->paginate) {
            $contracts = $query->paginate($request->paginate);
        } else {
            $contracts = $query->take($request->size)->get();
        }

        return $contracts;
    }

    /**
     * Show quotes list.
     * @param Request $request
     * @return JSON
     */
    public function quotes(Request $request)
    {
        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;
        $company_user_id = \Auth::user()->company_user_id;

        $query = QuoteV2::QuoteSelect()->ConditionalWhen($type, $status, $integration)
            ->AuthUserCompany($company_user_id)
            ->RateV2()->UserRelation()->CompanyRelation()
            ->ContactRelation()->PriceRelation()->SaletermRelation()
            ->with('incoterm')->orderBy('created_at', 'desc');

        if ($request->paginate) {
            $quotes = $query->paginate($request->paginate);
        } else {
            $quotes = $query->take($request->size)->get();
        }

        //Modify equipment array
        $this->transformEquipment($quotes);

        //Update Integration Quote Status
        if ($integration) {
            foreach ($quotes as $quote) {
                IntegrationQuoteStatus::where('quote_id', $quote->id)->update(['status' => 1]);
            }
        }

        $collection = Collection::make($quotes);

        if (! $request->paginate) {
            $collection->transform(function ($quote, $key) {
                unset($quote['origin_port_id']);
                unset($quote['destination_port_id']);
                unset($quote['origin_address']);
                unset($quote['destination_address']);
                unset($quote['currency_id']);

                return $quote;
            });
        }

        return $quotes;
    }

    /**
     * Show quotes list.
     * @param Request $request
     * @return JSON
     */
    public function quoteById(Request $request, $id)
    {
        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;
        $company_user_id = Auth::user()->company_user_id;

        $quote = QuoteV2::QuoteSelect()->ConditionalWhen($type, $status, $integration)
            ->AuthUserCompany($company_user_id)
            ->RateV2()->UserRelation()->CompanyRelation()
            ->ContactRelation()->PriceRelation()->SaletermRelation()
            ->with('incoterm')->findOrFail($id);

        //Modify equipment array
        $this->transformEquipmentSingle($quote);

        //Update Integration Quote Status
        /*if ($integration) {
            IntegrationQuoteStatus::where('quote_id', $quote->id)->update(['status' => 1]);
        }*/

        return $quote;
    }

    /**
     * Show carriers list.
     * @param Request $request
     * @return JSON
     */
    public function carriers(Request $request)
    {
        if ($request->paginate) {
            $carriers = Carrier::paginate($request->paginate);
        } else {
            $carriers = Carrier::take($request->size)->get();
        }

        return $carriers;
    }

    /**
     * Show airlines list.
     * @param Request $request
     * @return JSON
     */
    public function airlines(Request $request)
    {
        if ($request->paginate) {
            $airlines = Airline::paginate($request->paginate);
        } else {
            $airlines = Airline::take($request->size)->get();
        }

        return $airlines;
    }

    /**
     * Show surcharges list.
     * @param Request $request
     * @return JSON
     */
    public function surcharges(Request $request)
    {
        $name = $request->name;

        $query = Surcharge::when($name, function ($query, $name) {
            return $query->where('name', 'LIKE', '%'.$name.'%');
        })->where('company_user_id', \Auth::user()->company_user_id);

        if ($request->paginate) {
            $surcharges = $query->paginate($request->paginate);
        } else {
            $surcharges = $query->take($request->size)->get();
        }

        return $surcharges;
    }

    /**
     * Show ports list.
     * @param Request $request
     * @return JSON
     */
    public function ports(Request $request)
    {
        $name = $request->name;

        $query = Harbor::when($name, function ($query, $name) {
            return $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('code', 'LIKE', '%'.$name.'%');
        })->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation')->with('country');

        if ($request->paginate) {
            $ports = $query->paginate($request->paginate);
        } else {
            $ports = $query->take($request->size)->get();
        }

        return $ports;
    }

    /**
     * Show airports list.
     * @param Request $request
     * @return JSON
     */
    public function airports(Request $request)
    {
        $name = $request->name;
        $query = Airport::when($name, function ($query, $name) {
            return $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('code', 'LIKE', '%'.$name.'%');
        });

        if ($request->paginate) {
            $airports = $query->paginate($request->paginate);
        } else {
            $airports = $query->take($request->size)->get();
        }

        return $airports;
    }

    public function search($mode, $code_origin, $code_destination, $inicio, $fin, $group, $api_company_id = 0)
    {
        try {
            return $this->processSearch($mode, $code_origin, $code_destination, $inicio, $fin, $group, $api_company_id = 0);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }

    public function processSearch($mode, $code_origin, $code_destination, $inicio, $fin, $group, $api_company_id = 0)
    {
        $portOrig = Harbor::where('code', $code_origin)->firstOrFail();
        $portDest = Harbor::where('code', $code_destination)->firstOrFail();

        $origin_port[] = $portOrig->id;
        $origin_country[] = $portOrig->country_id;

        $destiny_port[] = $portDest->id;
        $destiny_country[] = $portDest->country_id;

        $company_user_id = \Auth::user()->company_user_id;
        $user_id = \Auth::id();
        $container_calculation = ContainerCalculation::get();
        $containers = Container::get();
        $companies = Company::where('api_id', '=', $api_company_id)->first();
        $company = CompanyUser::where('id', \Auth::user()->company_user_id)->first();

        $chargesOrigin = 'true';
        $chargesDestination = 'true';
        $chargesFreight = 'true';
        $markup = null;
        $remarks = '';
        $remarksGeneral = '';

        $equipment = [];
        $totalesCont = [];

        //Colecciones
        $general = new collection();
        $inlandDestiny = new collection();
        $inlandOrigin = new collection();
        $collectionRate = new Collection();

        $typeCurrency = $company->currency->alphacode;
        $idCurrency = $company->currency_id;
        $company_user_id = $company->id;
        $dateSince = $inicio;
        $dateUntil = $fin;
        $arreglo = null;

        if (empty($companies)) {
            $companies_id = 0;
        } else {
            $companies_id = $companies->id;
        }

        if (strtoupper($group) == 'DRY') {
            $equipment = ['1', '2', '3', '4', '5'];
        } elseif (strtoupper($group) == 'REEFER') {
            $equipment = ['6', '7', '8'];
        } elseif (strtoupper($group) == 'OPENTOP') {
            $equipment = ['9', '10'];
        } elseif (strtoupper($group) == 'FLATRACK') {
            $equipment = ['11', '12'];
        } else {
            abort(404);
        }

        $validateEquipment = $this->validateEquipment($equipment, $containers);

        // Consulta base de datos rates

        if ($validateEquipment['count'] < 2) {
            if ($companies_id != null || $companies_id != 0) {
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id) {
                    $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                        $a->where('user_id', '=', $user_id);
                    })->orDoesntHave('contract_user_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id) {
                    $q->whereHas('contract_company_restriction', function ($b) use ($companies_id) {
                        $b->where('company_id', '=', $companies_id);
                    })->orDoesntHave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('gp_container_id', '=', $validateEquipment['gpId']);
                })->with(['carrier' => function ($query) {
                    $query->select('id', 'name', 'uncode', 'image', 'image as url');
                }]);
            } else {
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_user_restriction');
                })->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('gp_container_id', '=', $validateEquipment['gpId']);
                })->with(['carrier' => function ($query) {
                    $query->select('id', 'name', 'uncode', 'image', 'image as url');
                }]);
            }
            $arreglo = $arreglo->get();
        }

        //Guard if
        if (count($arreglo) == 0) {
            return response()->json(['message' => 'No freight rates were found for this trade route'], 404);
        }

        foreach ($containers as $cont) {
            $totalesContainer = [$cont->code => ['tot_'.$cont->code.'_F' => 0, 'tot_'.$cont->code.'_O' => 0, 'tot_'.$cont->code.'_D' => 0]];
            $totalesCont = array_merge($totalesContainer, $totalesCont);
            $var = 'array'.$cont->code;
            $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
        }

        foreach ($arreglo as $data) {
            $contractStatus = $data->contract->status;
            $collectionRate = new Collection();
            $totalRates = 0;
            $totalT = 0;

            //Arreglo totalizador de freight , destination , origin por contenedor
            $totalesCont = [];
            $arregloRateSum = [];

            foreach ($containers as $cont) {
                $totalesContainer = [$cont->code => ['tot_'.$cont->code.'_F' => 0, 'tot_'.$cont->code.'_O' => 0, 'tot_'.$cont->code.'_D' => 0]];
                $totalesCont = array_merge($totalesContainer, $totalesCont);
                // Inicializar arreglo rate
                $arregloRate = ['c'.$cont->code => '0'];
                $arregloRateSum = array_merge($arregloRateSum, $arregloRate);
            }

            $carrier[] = $data->carrier_id;
            $orig_port = [$data->origin_port];
            $dest_port = [$data->destiny_port];

            $collectionOrigin = new collection();
            $collectionDestiny = new collection();
            $collectionFreight = new collection();

            $arregloRate = [];
            //Arreglos para guardar el rate
            $array_ocean_freight = ['type' => 'Ocean Freight', 'detail' => 'Per Container', 'currency' => $data->currency->alphacode];

            $arregloRateSave['markups'] = [];
            $arregloRateSave['rate'] = [];
            //Arreglo para guardar charges
            $arregloCharges['origin'] = [];

            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);
            // Rates
            $arregloR = $this->ratesSearch($equipment, $markup, $data, $rateC, $typeCurrency, $containers);
            $arregloRateSum = array_merge($arregloRateSum, $arregloR['arregloSaveR']);

            $arregloRateSave['rate'] = array_merge($arregloRateSave['rate'], $arregloR['arregloSaveR']);
            //$arregloRateSave['markups'] = array_merge($arregloRateSave['markups'], $arregloR['arregloSaveM']);
            $arregloRate = array_merge($arregloRate, $arregloR['arregloRate']);

            $equipmentFilter = $arregloR['arregloEquipment'];

            $port_all = Harbor::where('name', 'ALL')->select('id')->first();
            $carrier_all = Carrier::where('name', 'ALL')->select('id')->first();
            $country_all = Country::where('name', 'ALL')->select('id')->first();

            // id de los port  ALL
            array_push($orig_port, $port_all->id);
            array_push($dest_port, $port_all->id);
            // id de los carrier ALL
            array_push($carrier, $carrier_all);
            // Id de los paises
            array_push($origin_country, $country_all);
            array_push($destiny_country, $country_all);

            // ################### Calculos local  Charges #############################
            if ($contractStatus != 'api') {
                $localChar = LocalCharge::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                    $query->whereHas('localcharports', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('localcharcountries', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'surcharge.saleterm')
                    ->with(['currency' => function ($q) {
                        $q->select('id', 'alphacode', 'rates as exchange_usd', 'rates_eur as exchange_eur');
                    }])->get();
            } else {
                $localChar = LocalChargeApi::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                    $query->whereHas('localcharports', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('localcharcountries', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'surcharge.saleterm')
                    ->with(['currency' => function ($q) {
                        $q->select('id', 'alphacode', 'rates as exchange_usd', 'rates_eur as exchange_eur');
                    }])->get();
            }

            foreach ($localChar as $local) {
                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                foreach ($local->localcharcarriers as $localCarrier) {
                    if ($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id == $carrier_all->id) {
                        $localParams = ['terminos' => $terminos, 'local' => $local, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $localCarrier];
                        //Origin
                        if ($chargesOrigin != null) {
                            if ($local->typedestiny_id == '1') {
                                foreach ($containers as $cont) {
                                    $name_arreglo = 'array'.$cont->code;
                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionOrigin->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                    }
                                }
                            }
                        }
                        //Destiny
                        if ($chargesDestination != null) {
                            if ($local->typedestiny_id == '2') {
                                foreach ($containers as $cont) {
                                    $name_arreglo = 'array'.$cont->code;

                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionDestiny->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                    }
                                }
                            }
                        }
                        //Freight
                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {
                                $band = false;
                                //Se ajusta el calculo para freight tomando en cuenta el rate currency
                                $rateMount_Freight = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                                $localParams['typeCurrency'] = $data->currency->alphacode;
                                $localParams['idCurrency'] = $data->currency->id;
                                //Fin Variables

                                foreach ($containers as $cont) {
                                    $name_arreglo = 'array'.$cont->code;

                                    if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                        $collectionFreight->push($this->processLocalCharge($cont, $local, $localParams, $rateMount_Freight, $totalesCont));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ################## Fin local Charges        #############################
            //################## Calculos Global Charges #################################

            if ($contractStatus != 'api') {
                $globalChar = GlobalCharge::where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrier', function ($q) use ($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                    $query->orwhereHas('globalcharport', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('globalcharcountry', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    })->orwhereHas('globalcharportcountry', function ($q) use ($orig_port, $destiny_country) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('country_dest', $destiny_country);
                    })->orwhereHas('globalcharcountryport', function ($q) use ($origin_country, $dest_port) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('port_dest', $dest_port);
                    });
                })->where('company_user_id', '=', $company_user_id)->with('globalcharcarrier.carrier', 'currency', 'surcharge.saleterm')->get();

                foreach ($globalChar as $global) {
                    $rateMount = $this->ratesCurrency($global->currency->id, $typeCurrency);
                    // Condicion para enviar los terminos de venta o compra
                    if (isset($global->surcharge->saleterm->name)) {
                        $terminos = $global->surcharge->saleterm->name;
                    } else {
                        $terminos = $global->surcharge->name;
                    }
                    foreach ($global->globalcharcarrier as $globalCarrier) {
                        if ($globalCarrier->carrier_id == $data->carrier_id || $globalCarrier->carrier_id == $carrier_all->id) {
                            $globalParams = ['terminos' => $terminos, 'local' => $global, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $globalCarrier];
                            //Origin
                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    foreach ($containers as $cont) {
                                        $name_arreglo = 'array'.$cont->code;

                                        if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                            $collectionOrigin->push($this->processGlobalCharge($cont, $global, $globalParams, $rateMount, $totalesCont));
                                        }
                                    }
                                }
                            }

                            //Destiny
                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {
                                    $band = false;
                                    foreach ($containers as $cont) {
                                        $name_arreglo = 'array'.$cont->code;
                                        if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                            $collectionDestiny->push($this->processGlobalCharge($cont, $global, $globalParams, $rateMount, $totalesCont));
                                        }
                                    }
                                }
                            }
                            //Freight

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $rateMount_Freight = $this->ratesCurrency($global->currency->id, $data->currency->alphacode);
                                    $globalParams['typeCurrency'] = $data->currency->alphacode;
                                    $globalParams['idCurrency'] = $data->currency->id;
                                    //Fin Variables

                                    foreach ($containers as $cont) {
                                        $name_arreglo = 'array'.$cont->code;

                                        if (in_array($global->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                            $collectionFreight->push($this->processGlobalCharge($cont, $global, $globalParams, $rateMount_Freight, $totalesCont));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } // fin if contract Api
            // ############################ Fin global charges ######################

            $totalRates += $totalT;
            $array = ['type' => 'Ocean Freight', 'detail' => 'Per Container', 'subtotal' => $totalRates, 'total' => $totalRates.' '.$typeCurrency, 'idCurrency' => $data->currency_id, 'currency_rate' => $data->currency->alphacode, 'rate_id' => $data->id];
            $array = array_merge($array, $arregloRate);
            $array = array_merge($array, $arregloRateSave);
            $collectionRate->push($array);

            // SCHEDULE

            $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

            $data->setAttribute('via', $transit_time['via']);
            $data->setAttribute('transit_time', $transit_time['transit_time']);
            $data->setAttribute('service', $transit_time['service']);

            // Valores

            $data->setAttribute('rates', $collectionRate);
            $data->setAttribute('localfreight', $collectionFreight);
            $data->setAttribute('localdestiny', $collectionDestiny);
            $data->setAttribute('localorigin', $collectionOrigin);

            // Valores totales por contenedor
            $rateTot = $this->ratesCurrency($data->currency->id, $typeCurrency);

            $sum_origin = 'sum_origin_';
            $sum_freight = 'sum_freight_';
            $sum_destination = 'sum_destination_';

            foreach ($containers as $cont) {
                ${$sum_origin.$cont->code} = 0;
                ${$sum_freight.$cont->code} = 0;
                ${$sum_destination.$cont->code} = 0;
            }

            foreach ($containers as $cont) {
                foreach ($collectionOrigin as $origin) {
                    if ($cont->code == $origin['type']) {
                        $rateCurrency = $this->ratesCurrency($origin['currency_id'], $typeCurrency);
                        ${$sum_origin.$cont->code} += $origin['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionFreight as $freight) {
                    if ($cont->code == $freight['type']) {
                        $rateCurrency = $this->ratesCurrency($freight['currency_id'], $typeCurrency);
                        ${$sum_freight.$cont->code} += $freight['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionDestiny as $destination) {
                    if ($cont->code == $destination['type']) {
                        $rateCurrency = $this->ratesCurrency($destination['currency_id'], $typeCurrency);
                        ${$sum_destination.$cont->code} += $destination['price'] / $rateCurrency;
                    }
                }
            }

            foreach ($containers as $cont) {
                $totalesCont[$cont->code]['tot_'.$cont->code.'_F'] = $totalesCont[$cont->code]['tot_'.$cont->code.'_F'] + $arregloRateSum['c'.$cont->code];
                $data->setAttribute('tot'.$cont->code.'F', number_format($totalesCont[$cont->code]['tot_'.$cont->code.'_F'], 2, '.', ''));

                $data->setAttribute('tot'.$cont->code.'O', number_format($totalesCont[$cont->code]['tot_'.$cont->code.'_O'], 2, '.', ''));
                $data->setAttribute('tot'.$cont->code.'D', number_format($totalesCont[$cont->code]['tot_'.$cont->code.'_D'], 2, '.', ''));

                $totalesCont[$cont->code]['tot_'.$cont->code.'_F'] = $totalesCont[$cont->code]['tot_'.$cont->code.'_F'] / $rateTot;
                // TOTALES
                $name_tot = 'total'.$cont->code;
                $$name_tot = $totalesCont[$cont->code]['tot_'.$cont->code.'_D'] + $totalesCont[$cont->code]['tot_'.$cont->code.'_F'] + $totalesCont[$cont->code]['tot_'.$cont->code.'_O'];
                $$name_tot += ${$sum_origin.$cont->code} + ${$sum_freight.$cont->code} + ${$sum_destination.$cont->code};
                $data->setAttribute($name_tot, number_format($$name_tot, 2, '.', ''));
            }

            //remarks

            if ($data->contract->remarks != '') {
                $remarks = $data->contract->remarks.'<br>';
            }

            $remarksGeneral .= $this->remarksCondition($data->port_origin, $data->port_destiny, $data->carrier);

            $routes['origin_port'] = ['name' => $data->port_origin->name, 'code' => $data->port_origin->code];
            $routes['destination_port'] = ['name' => $data->port_destiny->name, 'code' => $data->port_destiny->code];
            $routes['ocean_freight'] = $array_ocean_freight;
            $routes['ocean_freight']['rates'] = $arregloRate;

            if ($mode == 'group') {
                if (! empty($collectionFreight)) {
                    $collectionFreight = $this->groupCollection($collectionFreight);
                    $routes['freight_charges'] = $collectionFreight;
                }

                if (! empty($collectionDestiny)) {
                    $collectionDestiny = $this->groupCollection($collectionDestiny);
                    $routes['destination_charges'] = $collectionDestiny;
                }

                if (! empty($collectionOrigin)) {
                    $collectionOrigin = $this->groupCollection($collectionOrigin);
                    $routes['origin_charges'] = $collectionOrigin;
                }
            } else {
                if (! empty($collectionFreight)) {
                    $routes['freight_charges'] = $collectionFreight;
                }

                if (! empty($collectionDestiny)) {
                    $routes['destination_charges'] = $collectionDestiny;
                }

                if (! empty($collectionOrigin)) {
                    $routes['origin_charges'] = $collectionOrigin;
                }
            }

            $detalle['Rates'] = $routes;

            //Totals
            foreach ($containers as $cont) {
                foreach ($equipment as $eq) {
                    if ($eq == $cont->id) {
                        $detalle['Rates']['total'.$cont->code] = $data['total'.$cont->code];
                    }
                }
            }

            $detalle['Rates']['currency'] = $typeCurrency;
            //Schedules
            $detalle['Rates']['schedule']['service'] = $data->service;
            $detalle['Rates']['schedule']['transit_time'] = $data->transit_time;
            $detalle['Rates']['schedule']['via'] = $data->via;

            //Set carrier
            $detalle['Rates']['carrier'] = $data->carrier;
            //Set contract details
            $detalle['Rates']['contract']['valid_from'] = $data->contract->validity;
            $detalle['Rates']['contract']['valid_until'] = $data->contract->expire;
            $detalle['Rates']['contract']['number'] = $data->contract->number;
            $detalle['Rates']['contract']['ref'] = $data->contract->name;
            $detalle['Rates']['contract']['status'] = $data->contract->status == 'publish' ? 'published' : $data->contract->status;

            $detalle['Rates']['remarks'] = $remarksGeneral.'<br>'.$remarks;

            $general->push($detalle);
        }

        return response()->json($general);
    }

    public function processSearchByContract(Request $request, $code)
    {
        try {
            $contract = Contract::where('code', $code)->first();
            $contract_lcl = ContractLcl::where('code', $code)->first();

            $response = $request->response;
            $convert = $request->convert;

            if ($contract != null) {
                return $contract->processSearchByIdFcl($response, $convert);
            } elseif ($contract_lcl != null) {
                return $contract_lcl->processSearchByIdLcl($response, $convert);
            } else {
                return response()->json(['message' => 'The requested contract does not exist'], 200);
            }
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }
}
