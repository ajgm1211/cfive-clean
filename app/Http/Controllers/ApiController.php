<?php

namespace App\Http\Controllers;

use App\Airline;
use App\Airport;
use App\CalculationType;
use App\Carrier;
use App\Company;
use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Contract;
use App\ContractLcl;
use App\Country;
use App\Currency;
use App\GlobalCharge;
use App\GlobalChargeLcl;
use App\Harbor;
use App\Http\Traits\MixPanelTrait;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\MediaStream;
use App\IntegrationQuoteStatus;
use App\LocalCharge;
use App\LocalChargeApi;
use App\LocalChargeLcl;
use App\OauthClient;
use App\QuoteV2;
use App\Rate;
use App\RateLcl;
use App\Surcharge;
use App\User;
use App\ViewGlobalCharge;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    use SearchTraitApi;
    use UtilTrait;
    use MixPanelTrait;

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

        $token->name = 'Password Grant Token ' . str_random(5);
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
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
        $tokenResult = $user->createToken($user->name . ' ' . $user->lastname . ' Token');
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
        if (!Auth::attempt($credentials)) {
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
                    'Authorization' => 'Bearer ' . $auth->access_token,
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

        if (!$request->paginate) {
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
     * Show quotes by ID
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
            $carriers = Carrier::select('id','name','uncode','scac','image')->paginate($request->paginate);
        } else {
            $carriers = Carrier::select('id','name','uncode','scac','image')->take($request->size)->get();
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
            return $query->where('name', 'LIKE', '%' . $name . '%');
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
            return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
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
            return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
        });

        if ($request->paginate) {
            $airports = $query->paginate($request->paginate);
        } else {
            $airports = $query->take($request->size)->get();
        }

        return $airports;
    }
    public function getCarrier($carrierUrl)
    {
        if ($carrierUrl == "all") {
            $carriers = Carrier::all()->pluck('id')->toArray();

        } else {
            $carriers = Carrier::where('name', $carrierUrl)->orWhere('uncode', $carrierUrl)->pluck('id')->toArray();
        }

        return $carriers;
    }
    public function search(Request $request, $mode, $code_origin, $code_destination, $inicio, $fin, $group, $carrierUrl = 'all', $api_company_id = 0)
    {
        try {

            $track_array = [];
            $track_array['origin'] = $code_origin;
            $track_array['destination'] = $code_destination;
            $track_array['from'] = $inicio;
            $track_array['until'] = $fin;
            $track_array['group'] = $group;

            /** Tracking search event with Mix Panel*/
            $this->trackEvents("api_rate_fcl", $track_array, "api");

            return $this->processSearch($mode, $code_origin, $code_destination, $inicio, $fin, $group, $carrierUrl, $api_company_id = 0, $request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }

    public function searchLCL(Request $request, $code_origin, $code_destination, $init_date, $end_date)
    {
        try {

            /* $track_array = [];
            $track_array['origin'] = $code_origin;
            $track_array['destination'] = $code_destination;
            $track_array['from'] = $inicio;
            $track_array['until'] = $fin;
            $track_array['group'] = $group;*/

            /** Tracking search event with Mix Panel*/
            // $this->trackEvents("api_rate_fcl", $track_array, "api");

            return $this->processSearchLCL($request, $code_origin, $code_destination, $init_date, $end_date);
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }

    public function processSearch($mode, $code_origin, $code_destination, $inicio, $fin, $group, $carrierUrl, $api_company_id = 0, $request)
    {
        if ($request->input('traffic') != null) {

            switch ($request->input('traffic')) {
                case 'import':
                    $traf = array('1');
                    break;
                case 'export':
                    $traf = array('2');
                    break;
                case 'both':
                    $traf = array('3');
                    break;
                default:
                    $traf = array('0');
                    break;
            }
        } else {
            $traf = array('1', '2', '3');
        }

        if (strtoupper($group) == 'DRY') {
            $equipment = array('1', '2', '3', '4', '5');
        } elseif (strtoupper($group) == 'REEFER') {
            $equipment = array('6', '7', '8');
        } elseif (strtoupper($group) == 'OPENTOP') {
            $equipment = array('9', '10');
        } elseif (strtoupper($group) == 'FLATRACK') {
            $equipment = array('11', '12');
        } else {
            abort(404);
        }

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
        $arregloCarrier = $this->getCarrier($carrierUrl);

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
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id, $traf) {
                    $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                        $a->where('user_id', '=', $user_id);
                    })->orDoesntHave('contract_user_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id) {
                    $q->whereHas('contract_company_restriction', function ($b) use ($companies_id) {
                        $b->where('company_id', '=', $companies_id);
                    })->orDoesntHave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment, $traf) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('gp_container_id', '=', $validateEquipment['gpId'])->whereIn('direction_id', $traf);
                })->with(['carrier' => function ($query) {
                    $query->select('id', 'name', 'uncode', 'image', 'image as url');
                }]);
            } else {
                $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->whereIn('carrier_id', $arregloCarrier)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_user_restriction');
                })->whereHas('contract', function ($q) {
                    $q->doesnthave('contract_company_restriction');
                })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $validateEquipment, $traf) {
                    $q->where(function ($query) use ($dateSince) {
                        $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                    })->where('company_user_id', '=', $company_user_id)->where('gp_container_id', '=', $validateEquipment['gpId'])->whereIn('direction_id', $traf);
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
            $totalesContainer = [$cont->code => ['tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0]];
            $totalesCont = array_merge($totalesContainer, $totalesCont);
            $var = 'array' . $cont->code;
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
                $totalesContainer = [$cont->code => ['tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0]];
                $totalesCont = array_merge($totalesContainer, $totalesCont);
                // Inicializar arreglo rate
                $arregloRate = ['c' . $cont->code => '0'];
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
            array_push($origin_country, $country_all->id);
            array_push($destiny_country, $country_all->id);

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
                                    $name_arreglo = 'array' . $cont->code;
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
                                    $name_arreglo = 'array' . $cont->code;

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
                                    $name_arreglo = 'array' . $cont->code;

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
                                        $name_arreglo = 'array' . $cont->code;

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
                                        $name_arreglo = 'array' . $cont->code;
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
                                        $name_arreglo = 'array' . $cont->code;

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
            $array = ['type' => 'Ocean Freight', 'detail' => 'Per Container', 'subtotal' => $totalRates, 'total' => $totalRates . ' ' . $typeCurrency, 'idCurrency' => $data->currency_id, 'currency_rate' => $data->currency->alphacode, 'rate_id' => $data->id];
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
                ${$sum_origin . $cont->code} = 0;
                ${$sum_freight . $cont->code} = 0;
                ${$sum_destination . $cont->code} = 0;
            }

            foreach ($containers as $cont) {
                foreach ($collectionOrigin as $origin) {
                    if ($cont->code == $origin['type']) {
                        $rateCurrency = $this->ratesCurrency($origin['currency_id'], $typeCurrency);
                        ${$sum_origin . $cont->code} += $origin['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionFreight as $freight) {
                    if ($cont->code == $freight['type']) {
                        $rateCurrency = $this->ratesCurrency($freight['currency_id'], $typeCurrency);
                        ${$sum_freight . $cont->code} += $freight['price'] / $rateCurrency;
                    }
                }
                foreach ($collectionDestiny as $destination) {
                    if ($cont->code == $destination['type']) {
                        $rateCurrency = $this->ratesCurrency($destination['currency_id'], $typeCurrency);
                        ${$sum_destination . $cont->code} += $destination['price'] / $rateCurrency;
                    }
                }
            }

            foreach ($containers as $cont) {
                $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $arregloRateSum['c' . $cont->code];
                $data->setAttribute('tot' . $cont->code . 'F', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_F'], 2, '.', ''));

                $data->setAttribute('tot' . $cont->code . 'O', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_O'], 2, '.', ''));
                $data->setAttribute('tot' . $cont->code . 'D', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_D'], 2, '.', ''));

                $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] / $rateTot;
                // TOTALES
                $name_tot = 'total' . $cont->code;
                $$name_tot = $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_O'];
                $$name_tot += ${$sum_origin . $cont->code}+${$sum_freight . $cont->code}+${$sum_destination . $cont->code};
                $data->setAttribute($name_tot, number_format($$name_tot, 2, '.', ''));
            }

            //remarks

            if ($data->contract->remarks != '') {
                $remarks = $data->contract->remarks . '<br>';
            }

            $remarksGeneral .= $this->remarksCondition($data->port_origin, $data->port_destiny, $data->carrier);

            $routes['type'] = 'FCL';
            // $routes['traffic'] = $traf;
            $routes['origin_port'] = array('name' => $data->port_origin->name, 'code' => $data->port_origin->code);
            $routes['destination_port'] = array('name' => $data->port_destiny->name, 'code' => $data->port_destiny->code);
            $routes['ocean_freight'] = $array_ocean_freight;
            $routes['ocean_freight']['rates'] = $arregloRate;

            if ($mode == 'group') {
                if (!empty($collectionFreight)) {
                    $collectionFreight = $this->groupCollection($collectionFreight);
                    $routes['freight_charges'] = $collectionFreight;
                }

                if (!empty($collectionDestiny)) {
                    $collectionDestiny = $this->groupCollection($collectionDestiny);
                    $routes['destination_charges'] = $collectionDestiny;
                }

                if (!empty($collectionOrigin)) {
                    $collectionOrigin = $this->groupCollection($collectionOrigin);
                    $routes['origin_charges'] = $collectionOrigin;
                }
            } else {
                if (!empty($collectionFreight)) {
                    $routes['freight_charges'] = $collectionFreight;
                }

                if (!empty($collectionDestiny)) {
                    $routes['destination_charges'] = $collectionDestiny;
                }

                if (!empty($collectionOrigin)) {
                    $routes['origin_charges'] = $collectionOrigin;
                }
            }

            $detalle['Rates'] = $routes;

            //Totals
            foreach ($containers as $cont) {
                foreach ($equipment as $eq) {
                    if ($eq == $cont->id) {
                        $detalle['Rates']['total' . $cont->code] = $data['total' . $cont->code];
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

            $detalle['Rates']['remarks'] = $remarksGeneral . '<br>' . $remarks;

            $general->push($detalle);
        }

        return response()->json($general);
    }

    public function processSearchByContract(Request $request, $code)
    {
        try {
            $contract = Contract::where('name', $code)->first();
            $contract_lcl = ContractLcl::where('name', $code)->first();

            $response = $request->response;
            $convert = $request->convert;

            if ($contract != null) {
                if ($contract->status == 'incomplete' || $contract->status == 'draft') {
                    return response()->json(['message' => 'The requested contract is pending processing', 'state' => 'CONVERSION_PENDING'], 200);
                } else {
                    return $contract->processSearchByIdFcl($response, $convert);
                }
            } elseif ($contract_lcl != null) {
                if ($contract_lcl->status == 'incomplete' || $contract_lcl->status == 'draft') {
                    return response()->json(['message' => 'The requested contract is pending processing', 'state' => 'CONVERSION_PENDING'], 200);
                } else {
                    return $contract_lcl->processSearchByIdLcl($response, $convert);
                }
            } else {
                return response()->json(['message' => 'The requested contract does not exist'], 200);
            }
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }

//************************************************************************************************** */

    public function processSearchLCL(Request $request, $code_origin, $code_destination, $init_date, $end_date)
    {

        //Variables del usuario conectado
        $company_user_id = \Auth::user()->company_user_id;
        $user_id = \Auth::id();
        $general = array();

        //Variables para cargar el  Formulario
        $chargesOrigin = true;
        $chargesDestination = true;
        $chargesFreight = true;

        // Request Formulario
        $portOrig = Harbor::where('code', $code_origin)->firstOrFail();
        $portDest = Harbor::where('code', $code_destination)->firstOrFail();

        $origin_port[] = $portOrig->id;
        $origin_country[] = $portOrig->country_id;

        $destiny_port[] = $portDest->id;
        $destiny_country[] = $portDest->country_id;

        $total_weight = $request->input('total_weight') ?? 1;
        $total_volume = $request->input('total_volume') ?? 1;
        $company_id = ($request->input('companyID') != null) ? $request->input('companyID') : null;

        //  $mode = $request->mode;

        $dateSince = $init_date;
        $dateUntil = $end_date;

        $total_weight = $total_weight / 1000;
        if ($total_volume > $total_weight) {
            $chargeable_weight = $total_volume;
        } else {
            $chargeable_weight = $total_weight;
        }

        $weight = $chargeable_weight;
        $weight = number_format($weight, 2, '.', '');
        // Fecha Contrato

        $company_user = User::where('id', \Auth::id())->first();
        $company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();
        $typeCurrency = 'USD';
        $idCurrency = 149;

        $currency_name = '';

        if ($company_setting->currency_id != null) {
            $currency_name = Currency::where('id', $company_user->companyUser->currency_id)->first();

            $typeCurrency = $company_setting->currency->alphacode;
            $idCurrency = $company_setting->currency_id;
        }

        $currencies = Currency::all()->pluck('alphacode', 'id');

        //Settings de la compañia
        $company = User::where('id', \Auth::id())->with('companyUser.currency')->first();

        $weight = number_format($weight, 2, '.', '');
        $collectionRate = new Collection();
        // Rates LCL

        $arreglo = RateLcl::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                $a->where('user_id', '=', $user_id);
            })->orDoesntHave('contract_user_restriction');
        })->whereHas('contract', function ($q) use ($user_id, $company_user_id, $company_id, $dateSince, $dateUntil) {
            $q->whereHas('contract_company_restriction', function ($b) use ($company_id) {
                $b->where('company_id', '=', $company_id);
            })->orDoesntHave('contract_company_restriction');
        })->whereHas('contract', function ($q) use ($company_user_id, $dateSince, $dateUntil, $company_setting) {
            if ($company_setting->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->where('company_user_id', '=', $company_user_id);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->where('company_user_id', '=', $company_user_id);
            }
        })->get();

        $collectionGeneral = new Collection();

        foreach ($arreglo as $data) {

            $tt = $data->transit_time;
            $va = $data->via;

            $totalFreight = 0;
            $FreightCharges = 0;
            $totalRates = 0;
            $totalOrigin = 0;
            $totalDestiny = 0;
            $totalQuote = 0;
            $totalAmmount = 0;
            $collectionOrig = new Collection();
            $collectionDest = new Collection();
            $collectionFreight = new Collection();
            $collectionGloOrig = new Collection();
            $collectionGloDest = new Collection();
            $collectionGloFreight = new Collection();
            $collectionRate = new Collection();

            $dataGOrig = array();
            $dataGDest = array();
            $dataGFreight = array();

            $dataOrig = array();
            $dataDest = array();
            $dataFreight = array();

            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);

            $typeCurrencyFreight = $data->currency->alphacode;
            $idCurrencyFreight = $data->currency->id;

            $subtotal = 0;

            $inlandDestiny = new Collection();
            $inlandOrigin = new Collection();
            $totalChargeOrig = 0;
            $totalChargeDest = 0;
            $totalInland = 0;

            if ($total_weight != null) {

                $simple = 'show active';
                $paquete = '';
                $subtotalT = $weight * $data->uom;
                $totalT = ($weight * $data->uom) / $rateC;
                $priceRate = $data->uom;

                if ($subtotalT < $data->minimum) {
                    $subtotalT = $data->minimum;
                    $totalT = $subtotalT / $rateC;
                    if ($weight < 1) {
                        $weightP = 1;
                    } else {
                        $weightP = $weight;
                    }

                    $priceRate = $data->minimum / $weightP;
                    $priceRate = number_format($priceRate, 2, '.', '');
                }

                $totalT = number_format($totalT, 2, '.', '');
                $totalFreight += $totalT;
                $totalRates += $totalT;

                $array = array('type' => 'Ocean Freight', 'quantity' => (float)$weight, 'detail' => 'W/M', 'price' => (float)$priceRate, 'currency' => $data->currency->alphacode, 'subtotal' => (float)$subtotalT, 'total' => (float)$totalT . " " . $typeCurrency );

                $collectionRate->push($array);

            }

            $data->setAttribute('rates', $collectionRate);

            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);
            $carrier[] = $data->carrier_id;

            // id de los port  ALL
            array_push($orig_port, 1485);
            array_push($dest_port, 1485);
            // id de los carrier ALL
            $carrier_all = 26;
            array_push($carrier, $carrier_all);
            // Id de los paises
            array_push($origin_country, 250);
            array_push($destiny_country, 250);

            //Calculation type
            $arrayBlHblShip = array('1', '2', '3', '16', '18', '20', '21'); // id  calculation type 1 = HBL , 2=  Shipment , 3 = BL , 16 per set
            $arraytonM3 = array('4', '11', '17'); //  calculation type 4 = Per ton/m3
            $arraytonCompli = array('6', '7', '12', '13'); //  calculation type 4 = Per ton/m3
            $arrayPerTon = array('5', '10'); //  calculation type 5 = Per  TON
            $arrayPerKG = array('9'); //  calculation type 5 = Per  TON
            $arrayPerPack = array('14'); //  per package
            $arrayPerPallet = array('15'); //  per pallet
            $arrayPerM3 = array('19'); //  per m3

            // Local charges
            $localChar = LocalChargeLcl::where('contractlcl_id', '=', $data->contractlcl_id)->whereHas('localcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                $query->whereHas('localcharportslcl', function ($q) use ($orig_port, $dest_port) {
                    $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                })->orwhereHas('localcharcountrieslcl', function ($q) use ($origin_country, $destiny_country) {
                    $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                });
            })->with('localcharportslcl.portOrig', 'localcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($localChar as $local) {

                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);
                $rateC = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                //Totales peso y volumen
                if ($request->input('total_weight') != null) {
                    $totalW = $request->input('total_weight') / 1000;
                    $totalV = $request->input('total_volume');
                    $totalWeight = $request->input('total_weight');
                } else {
                    $totalW = $request->input('total_weight_pkg') / 1000;
                    $totalV = $request->input('total_volume_pkg');
                    $totalWeight = $request->input('total_weight');
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                if (in_array($local->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            if ($company_setting->origincharge == '1') {
                                if ($chargesOrigin != null) {
                                    if ($local->typedestiny_id == '1') {
                                        $subtotal_local = $local->ammount;
                                        $totalAmmount = $local->ammount / $rateMount;
                                        // MARKUP
                                        //$markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                        $totalOrigin += $totalAmmount;
                                        $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                        $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                        $arregloOrig = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                        //$arregloOrig = array_merge($arregloOrig, $markupBL);

                                        $collectionOrig->push($arregloOrig);

                                        // ARREGLO GENERAL 99

                                        $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                        $collectionOrig->push($arregloOrigin);

                                    }
                                }
                            }
                            if ($company_setting->destinationcharge == '1') {
                                if ($chargesDestination != null) {
                                    if ($local->typedestiny_id == '2') {
                                        $subtotal_local = $local->ammount;
                                        $totalAmmount = $local->ammount / $rateMount;
                                        // MARKUP
                                        //   $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                        $totalDestiny += $totalAmmount;
                                        $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                        $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                        $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                        //   $arregloDest = array_merge($arregloDest, $markupBL);

                                        $collectionDest->push($arregloDest);

                                        // ARREGLO GENERAL 99

                                        $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                        $collectionDest->push($arregloDest);

                                        //return response()->json($collectionDest);
                                    }
                                }
                            }
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $local->ammount;
                                    $totalAmmount = $local->ammount / $rateC;

                                    // MARKUP
                                    // $markupBL = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => "-", 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //   $arregloPC = array_merge($arregloPC, $markupBL);

                                    $collectionFreight->push($arregloPC);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonM3)) {

                    //ROUNDED

                    if ($local->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTonM3 = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    // $arregloOrigTonM3 = array_merge($arregloOrigTonM3, $markupTonM3);

                                    $collectionOrig->push($arregloOrigTonM3);

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }
                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    // $arregloDest = array_merge($arregloDest, $markupTonM3);

                                    $collectionDest->push($arregloDest);

                                    // Arreglo 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }
                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    $subtotal_local = $ton_weight * $local->ammount;
                                    $totalAmmount = ($ton_weight * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        $mont = $local->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP
                                    //$markupTonM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);
                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //  $arregloPC = array_merge($arregloPC, $markupTonM3);

                                    $collectionFreight->push($arregloPC);

                                    // Arreglo 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($local->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    // $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigTon = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloOrigTon = array_merge($arregloOrigTon, $markupTON);
                                    $collectionOrig->push($arregloOrigTon);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateMount;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    //$markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloDest = array_merge($arregloDest, $markupTON);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalW * $local->ammount;
                                    $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = $subtotal_local / $rateC;
                                        $mont = $local->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP
                                    // $markupTON = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloPC = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //$arregloPC = array_merge($arregloPC, $markupTON);

                                    $collectionFreight->push($arregloPC);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {

                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }

                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    //   $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    //$totalAmmount =  number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'montoOrig' => $totalAmmount);
                                    // $arregloOrig = array_merge($arregloOrig, $markupTONM3);
                                    $dataOrig[] = $arregloOrig;

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $dataOrig[] = $arregloOrigin;
                                    //$collectionOrig->push($arregloOrigin);

                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateMount;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateMount;
                                            $mont = $local->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    //$markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'montoOrig' => $totalAmmount);
                                    //$arregloDest = array_merge($arregloDest, $markupTONM3);
                                    $dataDest[] = $arregloDest;

                                    // ARREGLO 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $dataDest[] = $arregloDest;
                                    //$collectionDest->push($arregloDest);

                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {
                                    if ($local->calculationtypelcl_id == '7' || $local->calculationtypelcl_id == '13') {
                                        if ($local->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_local = $totalV * $local->ammount;
                                        $totalAmmount = ($totalV * $local->ammount) / $rateC;
                                        $mont = $local->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateC;
                                            $mont = $local->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($local->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_local = $totalW * $local->ammount;
                                        $totalAmmount = ($totalW * $local->ammount) / $rateC;
                                        $mont = $local->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_local < $local->minimum) {
                                            $subtotal_local = $local->minimum;
                                            $totalAmmount = $subtotal_local / $rateC;
                                            if ($totalW < 1) {
                                                $mont = $local->minimum * $totalW;
                                            } else {
                                                $mont = $local->minimum / $totalW;
                                            }
                                        }
                                    }
                                    // Markup
                                    // $markupTONM3 = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloPC = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'calculation_id' => $local->calculationtypelcl->id, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //$arregloPC = array_merge($arregloPC, $markupTONM3);
                                    $dataFreight[] = $arregloPC;

                                    // ARREGLO 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $dataFreight[] = $arregloFreight;
                                    //$collectionFreight->push($arregloFreight);

                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($local->typedestiny_id == '1') {
                                    $subtotal_local = $totalWeight * $local->ammount;
                                    $totalAmmount = ($totalWeight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalWeight;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_local) / $rateMount;
                                        $unidades = $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloOrigKg = array_merge($arregloOrigKg, $markupKG);
                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalWeight * $local->ammount;
                                    $totalAmmount = ($totalWeight * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalWeight;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_local) / $rateMount;
                                        $unidades = $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestKg = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    // $arregloDestKg = array_merge($arregloDestKg, $markupKG);

                                    $collectionDest->push($arregloDestKg);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalWeight * $local->ammount;
                                    $totalAmmount = ($totalWeight * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $totalWeight;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_local) / $rateC;
                                        $unidades = $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightKg = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //$arregloFreightKg = array_merge($arregloFreightKg, $markupKG);

                                    $collectionFreight->push($arregloFreightKg);
                                    // ARREGLO GENERAL 99

                                    $arregloFreightKg = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreightKg);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                            if ($chargesOrigin != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpack = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloOrigpack = array_merge($arregloOrigpack, $markupKG);
                                    $collectionOrig->push($arregloOrigpack);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPack = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    // $arregloDestPack = array_merge($arregloDestPack, $markupKG);

                                    $collectionDest->push($arregloDestPack);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $package_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $package_cantidad * $local->ammount;
                                    $totalAmmount = ($package_cantidad * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $package_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_local) / $rateC;
                                        $unidades = $subtotal_local / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPack = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    //$arregloFreightPack = array_merge($arregloFreightPack, $markupKG);

                                    $collectionFreight->push($arregloFreightPack);
                                    // ARREGLO GENERAL 99

                                    $arregloFreightPack = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreightPack);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];
                            if ($chargesOrigin != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //   $arregloOrigpallet = array_merge($arregloOrigpallet, $markupKG);
                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);
                                    //$arregloDestPallet = array_merge($arregloDestPallet, $markupKG);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $pallet_cantidad != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $pallet_cantidad * $local->ammount;
                                    $totalAmmount = ($pallet_cantidad * $local->ammount) / $rateC;
                                    $mont = $local->ammount;
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    // $markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightPallet = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);
                                    // $arregloFreightPallet = array_merge($arregloFreightPallet, $markupKG);

                                    $collectionFreight->push($arregloFreightPallet);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($local->calculationtypelcl_id, $arrayPerM3)) {

                    foreach ($local->localcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($request->input('total_volume') != null) {
                                $totalVol = $request->input('total_volume');
                            } else {
                                $totalVol = $request->input('total_volume_pkg');
                            }

                            if ($chargesOrigin != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '1') {

                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP
                                    //$markupKG = $this->localMarkups($localPercentage, $localAmmount, $localMarkup, $totalAmmount, $typeCurrency, $markupLocalCurre);

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '2') {
                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateMount;
                                    $mont = $local->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMount;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $local->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $totalVol != 0) {
                                if ($local->typedestiny_id == '3') {

                                    $subtotal_local = $totalVol * $local->ammount;
                                    $totalAmmount = ($totalVol * $local->ammount) / $rateC;
                                    $mont = $local->ammount / $rateC;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $local->minimum) {
                                        $subtotal_local = $local->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    //$totalAmmount =  $local->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightVol = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $local->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreightVol);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'price_unit' => number_format($local->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtypelcl->name, 'contract_id' => $data->contract_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $local->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $local->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

            } // Fin del calculo de los local charges

            //######################## GLOBALES #########################

            $globalChar = GlobalChargeLcl::where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->whereHas('globalcharcarrierslcl', function ($q) use ($carrier) {
                $q->whereIn('carrier_id', $carrier);
            })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                $query->whereHas('globalcharportlcl', function ($q) use ($orig_port, $dest_port) {
                    $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                })->orwhereHas('globalcharcountrylcl', function ($q) use ($origin_country, $destiny_country) {
                    $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                });
            })->where('company_user_id', '=', $company_user_id)->with('globalcharportlcl.portOrig', 'globalcharportlcl.portDest', 'globalcharcarrierslcl.carrier', 'currency', 'surcharge.saleterm')->get();

            foreach ($globalChar as $global) {
                $rateMountG = $this->ratesCurrency($global->currency->id, $typeCurrency);
                $rateC = $this->ratesCurrency($global->currency->id, $data->currency->alphacode);

                if ($request->input('total_weight') != null) {
                    $totalW = $request->input('total_weight') / 1000;
                    $totalV = $request->input('total_volume');
                    $totalWeight = $request->input('total_weight');
                } else {
                    $totalW = $request->input('total_weight_pkg') / 1000;
                    $totalV = $request->input('total_volume_pkg');
                    $totalWeight = $request->input('total_weight');
                }

                // Condicion para enviar los terminos de venta o compra
                if (isset($global->surcharge->saleterm->name)) {
                    $terminos = $global->surcharge->saleterm->name;
                } else {
                    $terminos = $global->surcharge->name;
                }

                if (in_array($global->calculationtypelcl_id, $arrayBlHblShip)) {
                    $cantidadT = 1;
                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    //$origGlo["origin"] = $arregloOrig;
                                    $collectionOrig->push($arregloOrig);
                                    // $collectionGloOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => '1');

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateMountG;
                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => '1', 'monto' => $global->ammount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => '1');

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $global->ammount;
                                    $totalAmmount = $global->ammount / $rateC;

                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => '-', 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => '1');

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonM3)) {
                    //ROUNDED
                    if ($global->calculationtypelcl_id == '11') {
                        $ton_weight = ceil($weight);
                    } else {
                        $ton_weight = $weight;
                    }
                    $cantidadT = $ton_weight;

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $cantidadT);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDest);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $cantidadT);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {
                                    $subtotal_global = $ton_weight * $global->ammount;
                                    $totalAmmount = ($ton_weight * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateC;
                                        $mont = $global->minimum / $ton_weight;
                                        $mont = number_format($mont, 2, '.', '');
                                        $cantidadT = 1;
                                    }
                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $cantidadT, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $cantidadT, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $cantidadT);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerTon)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            //ROUNDED
                            if ($global->calculationtypelcl_id == '10') {
                                $totalW = ceil($totalW);
                            }
                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrig);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $mont = $global->minimum / $totalW;
                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDest);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalW * $global->ammount;
                                    $totalAmmount = ($totalW * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    $unidades = $this->unidadesTON($totalW);
                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateC;
                                        $mont = $global->minimum / $totalW;

                                        $mont = number_format($mont, 2, '.', '');
                                    }
                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreight);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arraytonCompli)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }

                                    // MARKUP

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrig = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'montoOrig' => $totalAmmount);

                                    $dataGOrig[] = $arregloOrig;

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $dataGOrig[] = $arregloOrigin;
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {
                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalV; // monto por unidad
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateMountG;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateMountG;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'montoOrig' => $totalAmmount);

                                    $dataGDest[] = $arregloDest;

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $dataGDest[] = $arregloDest;
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    if ($global->calculationtypelcl_id == '7' || $global->calculationtypelcl_id == '13') {
                                        if ($global->calculationtypelcl_id == '13') {
                                            $totalV = ceil($totalV);
                                        }
                                        $subtotal_global = $totalV * $global->ammount;
                                        $totalAmmount = ($totalV * $global->ammount) / $rateC;
                                        $mont = $global->ammount;
                                        $unidades = $totalV;
                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateC;
                                            $mont = $global->minimum / $totalV;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    } else {
                                        if ($global->calculationtypelcl_id == '12') {
                                            $totalW = ceil($totalW);
                                        }
                                        $subtotal_global = $totalW * $global->ammount;
                                        $totalAmmount = ($totalW * $global->ammount) / $rateC;
                                        $mont = $global->ammount;
                                        if ($totalW > 1) {
                                            $unidades = $totalW;
                                        } else {
                                            $unidades = '1';
                                        }

                                        if ($subtotal_global < $global->minimum) {
                                            $subtotal_global = $global->minimum;
                                            $totalAmmount = $subtotal_global / $rateC;
                                            $mont = $global->minimum / $totalW;
                                            $mont = number_format($mont, 2, '.', '');
                                        }
                                    }
                                    // MARKUP

                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'calculation_id' => $global->calculationtypelcl->id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $dataGFreight[] = $arregloFreight;

                                    // ARREGLO GENERAL 99
                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                    $dataGFreight[] = $arregloFreight;
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerKG)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = 10;
                                    // dd($subtotal_global,$global->minimum);

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = $subtotal_global / $rateMountG;
                                        $unidades = 10;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigKg = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrigKg);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateMountG;
                                        $unidades = $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDestKg);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $totalWeight * $global->ammount;
                                    $totalAmmount = ($totalWeight * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $totalWeight;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($totalWeight * $subtotal_global) / $rateC;
                                        $unidades = $totalWeight;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightKg = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreightKg);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPack)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $package_cantidad = $package_pallet['package']['cantidad'];
                            if ($chargesOrigin != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPack = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrigPack);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateMountG;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestKg = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDestPack);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $package_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $package_cantidad * $global->ammount;
                                    $totalAmmount = ($package_cantidad * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $package_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($package_cantidad * $subtotal_global) / $rateC;
                                        $unidades = $subtotal_global / $package_cantidad;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPack = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreightPack);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerPallet)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {
                            $pallet_cantidad = $package_pallet['pallet']['cantidad'];

                            if ($chargesOrigin != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloOrigPallet = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrigPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '2') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateMountG;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloDestPallet = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDestPallet);
                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $pallet_cantidad != '0') {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_global = $pallet_cantidad * $global->ammount;
                                    $totalAmmount = ($pallet_cantidad * $global->ammount) / $rateC;
                                    $mont = "";
                                    $unidades = $pallet_cantidad;

                                    if ($subtotal_global < $global->minimum) {
                                        $subtotal_global = $global->minimum;
                                        $totalAmmount = ($pallet_cantidad * $subtotal_global) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $subtotal_global = number_format($subtotal_global, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloFreightPallet = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_global' => $subtotal_global, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreightPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);

                                    $collectionFreight->push($arregloFreight);
                                }
                            }
                        }
                    }
                }

                if (in_array($global->calculationtypelcl_id, $arrayPerM3)) {

                    foreach ($global->globalcharcarrierslcl as $carrierGlobal) {
                        if ($carrierGlobal->carrier_id == $data->carrier_id || $carrierGlobal->carrier_id == $carrier_all) {

                            if ($request->input('total_volume') != null) {
                                $totalVol = $request->input('total_volume');
                            } else {
                                $totalVol = $request->input('total_volume_pkg');
                            }

                            if ($chargesOrigin != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '1') {

                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
                                    }

                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalOrigin += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    $arregloOrigpallet = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'origin', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionOrig->push($arregloOrigpallet);

                                    // ARREGLO GENERAL 99

                                    $arregloOrigin = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionOrig->push($arregloOrigin);
                                }
                            }

                            if ($chargesDestination != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '2') {
                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateMountG;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateMountG;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    $totalDestiny += $totalAmmount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $arregloDestPallet = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $totalAmmount, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrency, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'destination', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrency, 'montoOrig' => $totalAmmount);

                                    $collectionDest->push($arregloDestPallet);

                                    // ARREGLO GENERAL 99

                                    $arregloDest = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrency, 'currency_id' => $global->currency->id, 'cantidad' => $unidades);

                                    $collectionDest->push($arregloDest);
                                }
                            }

                            if ($chargesFreight != null && $totalVol != 0) {
                                if ($global->typedestiny_id == '3') {

                                    $subtotal_local = $totalVol * $global->ammount;
                                    $totalAmmount = ($totalVol * $global->ammount) / $rateC;
                                    $mont = $global->ammount;
                                    $unidades = $totalVol;

                                    if ($subtotal_local < $global->minimum) {
                                        $subtotal_local = $global->minimum;
                                        $totalAmmount = ($totalVol * $subtotal_local) / $rateC;
                                    }
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');

                                    // MARKUP

                                    //$totalAmmount =  $global->ammout  / $rateMount;
                                    $subtotal_local = number_format($subtotal_local, 2, '.', '');
                                    $totalAmmount = number_format($totalAmmount, 2, '.', '');
                                    $totalFreight += $totalAmmount;
                                    $FreightCharges += $totalAmmount;
                                    $arregloFreightVol = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'cantidad' => $unidades, 'monto' => $mont, 'currency' => $global->currency->alphacode, 'totalAmmount' => $totalAmmount . ' ' . $typeCurrencyFreight, 'calculation_name' => $global->calculationtypelcl->name, 'contract_id' => $data->contractlcl_id, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => 'freight', 'subtotal_local' => $subtotal_local, 'cantidad' => $unidades, 'typecurrency' => $typeCurrencyFreight, 'currency_orig_id' => $idCurrencyFreight, 'montoOrig' => $totalAmmount);

                                    $collectionFreight->push($arregloFreightVol);
                                    // ARREGLO GENERAL 99

                                    $arregloFreight = array('surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'price_unit' => number_format($global->ammount, 2, '.', ''), 'monto' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtypelcl->name, 'carrier_id' => $carrierGlobal->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtypelcl->id, 'montoOrig' => 0.00, 'typecurrency' => $typeCurrencyFreight, 'currency_id' => $global->currency->id, 'currency_orig_id' => $idCurrencyFreight, 'cantidad' => $unidades);
                                }
                            }
                        }
                    }
                }
            }

            //###################### FIN GLOBALES #######################

            // Locales

            if (!empty($dataOrig)) {
                $collectOrig = Collection::make($dataOrig);

                $m3tonOrig = $collectOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();

                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];
                        $collectionOrig->push($test);

                        return $test;
                    }
                });
            }

            if (!empty($dataDest)) {
                $collectDest = Collection::make($dataDest);
                $m3tonDest = $collectDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        //            $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataFreight)) {

                $collectFreight = Collection::make($dataFreight);
                $m3tonFreight = $collectFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalFreight += $totalA[0];
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            // Globales
            if (!empty($dataGOrig)) {
                $collectGOrig = Collection::make($dataGOrig);

                $m3tonGOrig = $collectGOrig->groupBy('surcharge_name')->map(function ($item) use ($collectionOrig, &$totalOrigin, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalOrigin += $totalA[0];

                        //$arre['origin'] = $test;
                        $collectionOrig->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGDest)) {
                $collectGDest = Collection::make($dataGDest);
                $m3tonDestG = $collectGDest->groupBy('surcharge_name')->map(function ($item) use ($collectionDest, &$totalDestiny, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalDestiny += $totalA[0];
                        // $arre['destiny'] = $test;
                        $collectionDest->push($test);
                        return $test;
                    }
                });
            }

            if (!empty($dataGFreight)) {

                $collectGFreight = Collection::make($dataGFreight);
                $m3tonFreightG = $collectGFreight->groupBy('surcharge_name')->map(function ($item) use ($collectionFreight, &$totalFreight, $data, $carrier_all) {
                    $carrArreglo = array($data->carrier_id, $carrier_all);
                    $test = $item->where('montoOrig', $item->max('montoOrig'))->wherein('carrier_id', $carrArreglo)->first();
                    if (!empty($test)) {
                        $totalA = explode(' ', $test['totalAmmount']);
                        $totalFreight += $totalA[0];
                        //$arre['freight'] = $test;
                        $collectionFreight->push($test);
                        return $test;
                    }
                });
            }

            //#######################################################################
            //Formato subtotales y operacion total quote
            $totalChargeOrig += $totalOrigin;
            $totalChargeDest += $totalDestiny;
            $totalFreight = number_format($totalFreight, 2, '.', '');
            $FreightCharges = number_format($FreightCharges, 2, '.', '');
            $totalOrigin = number_format($totalOrigin, 2, '.', '');
            $totalDestiny = number_format($totalDestiny, 2, '.', '');

            $totalFreightOrig = $totalFreight;

            $rateTotal = $this->ratesCurrency($data->currency->id, $typeCurrency);
            //$totalFreight = $totalFreight / $rateTotal;
            $totalFreight = number_format($totalFreight, 2, '.', '');

            $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
            $totalQuoteSin = number_format($totalQuote, 2, ',', '');

            if ($chargesDestination == null && $chargesOrigin == null) {

                $totalQuote = $totalFreightOrig;
                $data->setAttribute('quoteCurrency', $data->currency->alphacode);
            } else {
                $totalQuote = $totalFreight + $totalOrigin + $totalDestiny;
                $data->setAttribute('quoteCurrency', $typeCurrency);
            }

            if (!empty($collectionOrig)) {
                $collectionOrig = $this->OrdenarCollectionLCL($collectionOrig);
            }

            if (!empty($collectionDest)) {
                $collectionDest = $this->OrdenarCollectionLCL($collectionDest);
            }

            if (!empty($collectionFreight)) {
                $collectionFreight = $this->OrdenarCollectionLCL($collectionFreight);
            }

            //General information

            $status = $data->contract->status;

            if($data->contract->status == 'publish'){
                $status = "published";
            }

            $information['contract'] = array('name'=>$data->contract->name,'valid_from'=>$data->contract->validity,'valid_until'=>$data->contract->expire,'status'=>$status,'contract_id'=> $data->contract->id,'rate_id' => $data->id, 'uom' => $data->uom, 'minimum' => $data->minimum, 'transit_time' => $data->transit_time, 'via' => $data->via, 'created_at' => $data->contract->created_at, 'updated_at' => $data->contract->updated_at);
            $information['contract']['origin_port'] = array('id' => $data->port_origin->id, 'name' => $data->port_origin->display_name, 'code' => $data->port_origin->code, 'coordinates' => $data->port_destiny->coordinates);
            $information['contract']['destination_port'] = array('id' => $data->port_destiny->id, 'name' => $data->port_destiny->display_name, 'code' => $data->port_destiny->code, 'coordinates' => $data->port_destiny->coordinates);
            $information['contract']['carrier'] = array('id' => $data->carrier->id, 'name' => $data->carrier->name, 'code' => $data->carrier->uncode, 'scac' => $data->carrier->scac, 'image' => $data->carrier->image, 'url' => $data->carrier->url.$data->carrier->image);
            $information['contract']['ocean_freight'] = $array;
            $information['contract']['freight_charges'] = $collectionFreight;
            $information['contract']['origin_charges'] = $collectionOrig;
            $information['contract']['destination_charges'] = $collectionDest;
            $information['contract']['totals'] = array('freight' => $FreightCharges, 'origin' => $totalOrigin, 'destination' => $totalDestiny,'rates'=> number_format($totalRates, 2, '.', ''),'all_freight'=>$totalFreight,'quote'=> number_format($totalQuote, 2, '.', ''));


            $collectionGeneral->push( $information);

            // INLANDS

            $data->setAttribute('totalChargeOrig', $totalChargeOrig);
            $data->setAttribute('totalChargeDest', $totalChargeDest);

            //Total quote atributes

            //    $data->setAttribute('rateCurrency', $data->currency->alphacode);
            //   $data->setAttribute('totalQuoteSin', $totalQuoteSin);
            //    $data->setAttribute('idCurrency', $idCurrency);
            // SCHEDULES
            //  $data->setAttribute('schedulesFin', "");

            // Ordenar las colecciones

        }

        $collectionGeneral = $collectionGeneral->sortBy('totalQuote');

        return response()->json($collectionGeneral);
/*

$mixSearch = array();
$company_setting = CompanyUser::where('id', \Auth::user()->company_user_id)->first();

if (isset($request->contact_id) && isset($request->company_id_quote)) {
$contact = contact::find($request->contact_id);

$contact_cliente = $contact->first_name . ' ' . $contact->last_name;
$company_cliente = $companies[$request->company_id_quote];
} else {
$contact_cliente = null;
$company_cliente = null;
}*/
    }

    public function OrdenarCollectionLCL($collection)
    {

        $collection = $collection->groupBy([
            'surcharge_name', 'calculation_name',
            function ($item) {
                return $item['type'];
            },
        ], $preserveKeys = true);

        // Se Ordena y unen la collection
        $collect = new collection();
        $monto = 0;

        foreach ($collection as $item) {
            foreach ($item as $items) {

                $total = count($items);

                if ($total > 1) {
                    foreach ($items as $itemsT) {
                        foreach ($itemsT as $itemsDetail) {
                            $monto += $itemsDetail['monto'];

                        }
                    }
                    $itemsDetail['monto'] = number_format($monto, 2, '.', '');
                    $itemsDetail['currency'] = $itemsDetail['typecurrency'];
                    $itemsDetail['type'] = 0;

                    $itemsDetail['price_unit'] = $total; //$itemsDetail['monto'] /  $itemsDetail['cantidad'];
                    //$itemsDetail['currency_id'] = $itemsDetail['currency_orig_id'];

                    unset($itemsDetail['montoOrig']);
                    unset($itemsDetail['contract_id']);

                    unset($itemsDetail['calculation_id']);
                    unset($itemsDetail['typecurrency']);
                    unset($itemsDetail['currency_id']);
                    unset($itemsDetail['carrier_id']);
                    unset($itemsDetail['currency_orig_id']);

                    $collect->push($itemsDetail);
                    $monto = 0;

                } else {
                    foreach ($items as $itemsT) {
                        foreach ($itemsT as $itemsDetail) {
                            $itemsDetail['monto'] = number_format($itemsDetail['montoOrig'], 2, '.', '');

                            unset($itemsDetail['montoOrig']);
                            unset($itemsDetail['contract_id']);

                            unset($itemsDetail['calculation_id']);
                            unset($itemsDetail['typecurrency']);
                            unset($itemsDetail['currency_id']);
                            unset($itemsDetail['totalAmmount']);

                            unset($itemsDetail['carrier_id']);

                            unset($itemsDetail['subtotal_local']);

                            unset($itemsDetail['currency_orig_id']);
                            $itemsDetail['type'] = 0;
                            $collect->push($itemsDetail);
                            $monto = 0;
                            $montoMarkup = 0;
                            $totalMarkup = 0;
                        }
                    }
                }
            }
        }

        $collect = $collect->groupBy([
            'surcharge_name',
            function ($item) use ($collect) {
                // $collect->put('x', 'surcharge_name');
                return $item['type'];
            },
        ], $preserveKeys = false);

        return $collect;
    }

    //
    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }

    public function getAmountType($type)
    {
        switch ($type) {
            case 1:
                return 'Origin';
                break;
            case 2:
                return 'Destination';
                break;
            case 3:
                return 'Freight';
                break;
        }
    }

    public function getContract(Request $request)
    {
        if(!$request->carrier || !$request->container || !$request->direction || !$request->since || !$request->until){
            return response()->json(['message' => 'There are missing parameters. You must send direction, carrier, since, until and container'], 400);
        }

        $direction = $request->input('direction'); //'2020/10/01';

        $collectionGeneral = new Collection();
        $code = $request->input('container'); //'2020/10/01';
        $containers = Container::where('gp_container_id', $code)->get();
        $contArray = $containers->pluck('code')->toArray();
        $dateSince = $request->input('since');
        $dateUntil = $request->input('until');
        $resultado['contract']['surcharge'] = array();

        $company_user_id = \Auth::user()->company_user_id;

        $company_setting = CompanyUser::where('id', $company_user_id)->first();
        $container_calculation = ContainerCalculation::get();
        $resultado = array();

        if ($direction == 3) {
            $direction = array(1, 2, 3);
        } else {
            $direction = array($direction);
        }
        $carrier = $this->getCarrier($request->carrier);
        $reference = $request->reference;
        
            $arreglo = Rate::whereIn('carrier_id',$carrier)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $company_setting, $direction, $code,$reference) {
            if ($company_setting->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->when($reference, function($query,$name){
                    return $query->where('name','LIKE','%'.$name.'%');
                })->where('company_user_id', '=', $company_user_id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->when($reference, function($query,$name){
                    return $query->where('name','LIKE','%'.$name.'%');
                })->where('company_user_id', '=', $company_user_id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code);
            }
        })->orderBy('contract_id')->get();

        $a = 2;
        $contractId = -1;
        foreach ($arreglo as $data) {

            $montos = array();
            $montos2 = array();
            $montosAllIn = array();
            $montosAllInTot = array();
       
            foreach ($containers as $cont) {
                $name_rate = 'rate' . $cont->code;

                $var = 'array' . $cont->code;
                $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
       
                $options = json_decode($cont->options);
                //dd($options);
                if (@$options->field_rate == 'containers') {
                    $test = json_encode($data->{$options->field_rate});
                    $jsonContainer = json_decode($data->{$options->field_rate});
                    if (isset($jsonContainer->{'C' . $cont->code})) {
                        $rateMount = $jsonContainer->{'C' . $cont->code};
                        $$name_rate = $rateMount;
                        $montosAllIn = array($cont->code => (float)$$name_rate);
                    } else {
                        $rateMount = 0;
                        $$name_rate = $rateMount;
                        $montosAllIn = array($cont->code => (float)$$name_rate);
                    }
                } else {
                    $rateMount = $data->{$options->field_rate};
                    $$name_rate = $rateMount;
                    $montosAllIn = array($cont->code => (float)$$name_rate);
                }

                $montos2 = array($cont->code => (float)$rateMount);
                $montos = array_merge($montos, $montos2);
                $montosAllInTot = array_merge($montosAllInTot, $montosAllIn);

            }
            $arrayFirstPartAmount = array(
                'contract' => $data->contract->name,
                'reference' => $data->contract->id,
                'carrier' => $data->carrier->name,
                'direction' => $data->contract->direction->name,
                'origin' => ucwords(strtolower($data->port_origin->name)),
                'destination' => ucwords(strtolower($data->port_destiny->name)),
                'valid_from' => $data->contract->validity,
                'valid_until' => $data->contract->expire,
            );
            //$arrayFirstPartAmount = array_merge($arrayFirstPartAmount, $montos);
            $arraySecondPartAmount = array(
                'charge' => 'freight',
                'currency' => $data->currency->alphacode,

            );
            //$arrayCompleteAmount = array_merge($arrayFirstPartAmount, $arraySecondPartAmount);
            $ocean_freight = array_merge($montos, $arraySecondPartAmount);
            $resultado['contract']['general'] = $arrayFirstPartAmount;
            $resultado['contract']['ocean_freight'] = $ocean_freight;

            $a++;
            // Local charges
            if ($contractId != $data->contract->id) {

                $contractId = $data->contract->id;
                $data1 = \DB::select(\DB::raw('call proc_localchar(' . $data->contract->id . ')'));
                $arrayCompleteLocal = array();
                $resultado['contract']['surcharges'] = array();
                if ($data1 != null) {
                    for ($i = 0; $i < count($data1); $i++) {
                        //'country_orig' =>  $data1[$i]->country_orig,
                        //  'country_dest' =>   $data1[$i]->country_dest,
                        $montosLocal = array();
                        $montosLocal2 = array();
                        $arrayFirstPartLocal = array(
                            //'Contract' => $data->contract->name,
                            //'Reference' => $data->contract->id,
                            'charge' => $data1[$i]->surcharge,

                        );

                        $calculationID = CalculationType::where('name', $data1[$i]->calculation_type)->first();
                        $currencyID = Currency::where('alphacode', $data1[$i]->currency)->first();
                        
                        foreach ($containers as $cont) {
                            $name_arreglo = 'array' . $cont->code;
                            $name_rate = 'rate' . $cont->code;
                            if (in_array($calculationID->id, $$name_arreglo)) {
                                $monto = $this->perTeu($data1[$i]->ammount, $calculationID->id, $cont->code);
                                $currency_rate = $this->ratesCurrency($currencyID->id, $data->currency->alphacode);
                                $$name_rate = number_format($$name_rate + ($monto / $currency_rate), 2, '.', '');
                                $montosAllInTot[$cont->code] = (float)$$name_rate;
                                $montosLocal2 = array($cont->code => (float)$monto);
                                $montosLocal = array_merge($montosLocal, $montosLocal2);
                            } else {
                                $montosLocal2 = array($cont->code => '0');

                                $montosLocal = array_merge($montosLocal, $montosLocal2);
                            }
                        }
                        $arrayFirstPartLocal = array_merge($arrayFirstPartLocal, $montosLocal);

                        $arraySecondPartLocal = array(
                            'currency' => $data1[$i]->currency,

                        );
                        $resultado['contract']['surcharges'][] = array_merge($arrayFirstPartLocal, $arraySecondPartLocal);
                       // $resultado['contract']['surcharge'][] = $arrayCompleteLocal;

                    }
                }
            }
            // MONTOS ALL IN
            $arrayFirstPartAmountAllIn = array(
                'charge' => 'freight - ALL IN',
            );
            $arrayFirstPartAmountAllIn = array_merge($arrayFirstPartAmountAllIn, $montosAllInTot);
            $arraySecondPartAmountAllIn = array(
                'currency' => $data->currency->alphacode,
            );
            $arrayCompleteAmountAllIn = array_merge($arrayFirstPartAmountAllIn, $arraySecondPartAmountAllIn);
            $resultado['contract']['allIn'] = $arrayCompleteAmountAllIn;
            $collectionGeneral->push($resultado);

        }
        return response()->json($collectionGeneral, 200);

    }

    public function perTeu($monto, $calculation_type, $code)
    {
        $arrayTeu = CalculationType::where('options->isteu', true)->pluck('id')->toArray();
        $codeArray = Container::where('code', 'like', '20%')->pluck('code')->toArray();

        if (!in_array($code, $codeArray)) {
            if (in_array($calculation_type, $arrayTeu)) {
                $monto = $monto * 2;
                return $monto;
            } else {
                return $monto;
            }
        } else {
            return $monto;
        }
    }

    public function pdfApi($id)
    {
        $quote = QuoteV2::where('id', $id)->orwhere('quote_id', $id)->first();
        if (!empty($quote)) {
            $mediaItem = Media::where('model_id', $quote->id)->where('model_type', 'App\QuoteV2')->first();
            if (!empty($mediaItem)) {
                $data = array(
                    "url_to_download" => $quote->getMedia('document')->first()->getUrl(),
                    'quote_id' => $quote->quote_id
                );
                return $data;
            } else {
                return response()->json('Sorry, the media file does not exist');
            }
        } else {
            return response()->json('Sorry, the quote does not exist');
        }
    }

}
