<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\OauthClient;
use App\User;
use App\Rate;
use App\Contract;
use App\GlobalCharPort;
use App\GlobalCharCountry;
use App\LocalCharPort;
use App\LocalCharCountry;
use App\LocalCharge;
use App\OauthAccessToken;
use App\ViewLocalCharges;
use App\ViewGlobalCharge;
use App\Currency;
use App\Harbor;
use App\Company;
use App\Country;
use App\CompanyUser;
use App\QuoteV2;
use App\Carrier;
use App\Airline;
use App\Airport;
use App\Http\Resources\SurchargeResource;
use App\Surcharge;
use App\IntegrationQuoteStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function index()
    {

        if (\Auth::user()->type == 'company') {
            $tokens = OauthClient::where('company_user_id', \Auth::user()->company_user_id)->get();
        } else if (\Auth::user()->type == 'admin') {
            $tokens = OauthClient::all();
        }
        return view('oauth.index', compact('tokens'));
    }

    public function createAccessToken()
    {

        $token = new OauthClient();

        $token->name = "Password Grant Token " . str_random(5);
        $token->company_user_id = \Auth::user()->company_user_id;
        $token->secret = str_random(40);
        $token->redirect = "http://localhost";
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
            'message' => 'Successfully created user!'
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
     * Login user and create token
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
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function deleteToken(Request $request, $id)
    {
        $token = OauthClient::find($id);
        $token->delete();

        return response()->json([
            'message' => 'Ok'
        ]);
    }

    /**
     * Logout from session
     * @param Request $request 
     * @return JSON
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>
        'Successfully logged out']);
    }

    /**
     * Show user info
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
                ]
            ]);
            // You'd typically save this payload in the session
            $auth = json_decode((string) $response->getBody());
            //dd($auth->access_token);
            $response = $client->get('http://cargofive.dev.com/api/v1/quotes', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Authorization' => 'Bearer ' . $auth->access_token,
                ]
            ]);

            $all = json_decode((string) $response->getBody());
            dd(json_encode($all));
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            echo "Unable to retrieve access token.";
        }
    }

    /**
     * Show FCL Rates list
     * @param Request $request 
     * @return JSON
     */

    public function rates(Request $request)
    {
        if ($request->paginate) {
            $rates = Rate::whereHas('contract', function ($q) {
                $q->where('contracts.company_user_id', \Auth::user()->company_user_id);
            })->with('contract')->paginate($request->paginate);
        } else {
            $rates = Rate::whereHas('contract', function ($q) {
                $q->where('contracts.company_user_id', \Auth::user()->company_user_id);
            })->with('contract')->take($request->size)->get();
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
     * Show charges list
     * @param Request $request 
     * @return JSON
     */

    public function charges(Request $request)
    {

        if ($request->paginate) {
            $charges = ViewLocalCharges::whereHas('contract', function ($q) {
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
            )->with('contract')->paginate($request->paginate);
        } else {
            $charges = ViewLocalCharges::whereHas('contract', function ($q) {
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
            )->take($request->size)->with('contract')->get();
        }

        return $charges;
    }

    /**
     * Show globalcharge list
     * @param Request $request 
     * @return JSON
     */

    public function globalCharges(Request $request)
    {
        if ($request->size) {
            $charges = ViewGlobalCharge::where('company_user_id', \Auth::user()->company_user_id)->take($request->size)->get();
        } else {
            $charges = ViewGlobalCharge::where('company_user_id', \Auth::user()->company_user_id)->get();
        }

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
     * Show contracts list
     * @param Request $request 
     * @return JSON
     */
    public function contracts(Request $request)
    {
        if ($request->paginate) {
            $contracts = Contract::where('company_user_id', '=', Auth::user()->company_user_id)->paginate($request->paginate);
        } else {
            $contracts = Contract::where('company_user_id', '=', Auth::user()->company_user_id)->take($request->size)->get();
        }

        return $contracts;
    }

    /**
     * Show quotes list
     * @param Request $request 
     * @return JSON
     */

    public function quotes(Request $request)
    {
        $company_user = null;
        $currency_cfg = null;
        $type = $request->type;
        $status = $request->status;
        $integration = $request->integration;
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            if ($request->paginate) {
                $quotes = QuoteV2::when($type, function ($query, $type) {
                    return $query->where('type', $type);
                })->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })->when($integration, function ($query, $integration) {
                    return $query->whereHas('integration', function ($q) {
                        $q->where('status', 0);
                    });
                })->where('user_id', \Auth::user()->id)->whereHas('user', function ($q) use ($company_user_id) {
                    $q->where('company_user_id', '=', $company_user_id);
                })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                    $query->with('origin_airport', 'destination_airport', 'currency', 'airline');
                    $query->with(['origin_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['destination_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['charge' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['charge_lcl_air' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['carrier' => function ($q) {
                        $q->select('id', 'name', 'uncode', 'varation as variation');
                    }]);
                    $query->with('inland');
                    $query->with('automaticInlandLclAir');
                }])->with(['user' => function ($query) {
                    $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state', 'company_user_id');
                    $query->with(['companyUser' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                }])->with(['company' => function ($query) {
                    $query->with(['company_user' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                    $query->with(['owner' => function ($q) {
                        $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state');
                    }]);
                }])->with(['contact' => function ($query) {
                    $query->with(['company' => function ($q) {
                        $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
                    }]);
                }])->with(['price' => function ($q) {
                    $q->select('id', 'name', 'description');
                }])->with(['saleterm' => function ($q) {
                    $q->with('charge');
                }])->with('incoterm')->paginate($request->paginate);
            } else {
                $quotes = QuoteV2::when($type, function ($query, $type) {
                    return $query->where('type', $type);
                })->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })->when($integration, function ($query, $integration) {
                    return $query->whereHas('integration', function ($q) {
                        $q->where('status', 0);
                    });
                })->where('user_id', \Auth::user()->id)->whereHas('user', function ($q) use ($company_user_id) {
                    $q->where('company_user_id', '=', $company_user_id);
                })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                    $query->with('origin_airport', 'destination_airport', 'currency', 'airline');
                    $query->with(['origin_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['destination_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['charge' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['charge_lcl_air' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['carrier' => function ($q) {
                        $q->select('id', 'name', 'uncode', 'varation as variation');
                    }]);
                    $query->with('inland');
                    $query->with('automaticInlandLclAir');
                }])->with(['user' => function ($query) {
                    $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state', 'company_user_id');
                    $query->with(['companyUser' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                }])->with(['company' => function ($query) {
                    $query->with(['company_user' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                    $query->with(['owner' => function ($q) {
                        $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state');
                    }]);
                }])->with(['contact' => function ($query) {
                    $query->with(['company' => function ($q) {
                        $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
                    }]);
                }])->with(['price' => function ($q) {
                    $q->select('id', 'name', 'description');
                }])->with(['saleterm' => function ($q) {
                    $q->with('charge');
                }])->with('incoterm')->take($request->size)->get();
            }
        } else {
            if ($request->paginate) {
                $quotes = QuoteV2::when($type, function ($query, $type) {
                    return $query->where('type', $type);
                })->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })->when($integration, function ($query, $integration) {
                    return $query->whereHas('integration', function ($q) {
                        $q->where('status', 0);
                    });
                })->whereHas('user', function ($q) use ($company_user_id) {
                    $q->where('company_user_id', '=', $company_user_id);
                })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                    $query->with('origin_airport', 'destination_airport', 'currency', 'airline');
                    $query->with(['origin_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['destination_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['charge' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['charge_lcl_air' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['carrier' => function ($q) {
                        $q->select('id', 'name', 'uncode', 'varation as variation');
                    }]);
                    $query->with('inland');
                    $query->with('automaticInlandLclAir');
                }])->with(['user' => function ($query) {
                    $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state', 'company_user_id');
                    $query->with(['companyUser' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                }])->with(['company' => function ($query) {
                    $query->with(['company_user' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                    $query->with(['owner' => function ($q) {
                        $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state');
                    }]);
                }])->with(['contact' => function ($query) {
                    $query->with(['company' => function ($q) {
                        $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
                    }]);
                }])->with(['price' => function ($q) {
                    $q->select('id', 'name', 'description');
                }])->with(['saleterm' => function ($q) {
                    $q->with('charge');
                }])->with('incoterm')->paginate($request->paginate);
            } else {
                $quotes = QuoteV2::when($type, function ($query, $type) {
                    return $query->where('type', $type);
                })->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })->when($integration, function ($query, $integration) {
                    return $query->whereHas('integration', function ($q) {
                        $q->where('status', 0);
                    });
                })->whereHas('user', function ($q) use ($company_user_id) {
                    $q->where('company_user_id', '=', $company_user_id);
                })->orderBy('created_at', 'desc')->with(['rates_v2' => function ($query) {
                    $query->with('origin_airport', 'destination_airport', 'currency', 'airline');
                    $query->with(['origin_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['destination_port' => function ($q) {
                        $q->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation', 'api_varation as api_variation');
                        $q->with('country');
                    }]);
                    $query->with(['charge' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['charge_lcl_air' => function ($q) {
                        $q->with('type', 'surcharge', 'calculation_type', 'currency');
                    }]);
                    $query->with(['carrier' => function ($q) {
                        $q->select('id', 'name', 'uncode', 'varation as variation');
                    }]);
                    $query->with('inland');
                    $query->with('automaticInlandLclAir');
                }])->with(['user' => function ($query) {
                    $query->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state', 'company_user_id');
                    $query->with(['companyUser' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                }])->with(['company' => function ($query) {
                    $query->with(['company_user' => function ($q) {
                        $q->select('id', 'name', 'address', 'phone', 'currency_id');
                        $q->with('currency');
                    }]);
                    $query->with(['owner' => function ($q) {
                        $q->select('id', 'name', 'lastname', 'email', 'phone', 'type', 'name_company', 'position', 'access', 'verified', 'state');
                    }]);
                }])->with(['contact' => function ($query) {
                    $query->with(['company' => function ($q) {
                        $q->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number');
                    }]);
                }])->with(['price' => function ($q) {
                    $q->select('id', 'name', 'description');
                }])->with(['saleterm' => function ($q) {
                    $q->with('charge');
                }])->with('incoterm')->take($request->size)->get();
            }
        }

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

        return $collection;
    }

    /**
     * Show carriers list
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
     * Show airlines list
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
     * Show surcharges list
     * @param Request $request 
     * @return JSON
     */

    public function surcharges(Request $request)
    {

        $name = $request->name;

        if ($request->paginate) {
            $surcharges = Surcharge::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%');
            })->where('company_user_id', \Auth::user()->company_user_id)->paginate($request->paginate);
        } else {
            $surcharges = Surcharge::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%');
            })->where('company_user_id', \Auth::user()->company_user_id)->take($request->size)->get();
        }

        return $surcharges;
    }

    /**
     * Show ports list
     * @param Request $request 
     * @return JSON
     */

    public function ports(Request $request)
    {
        $name = $request->name;

        if ($request->paginate) {
            $ports = Harbor::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
            })->select('id','name','code','display_name','coordinates','country_id','varation as variation')
            ->with('country')->paginate($request->paginate);
        } else {
            $ports = Harbor::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
            })->with('country')
            ->select('id','name','code','display_name','coordinates','country_id','varation as variation')->take($request->size)->get();
        }
        
        return $ports;
    }

    /**
     * Show airports list
     * @param Request $request 
     * @return JSON
     */

    public function airports(Request $request)
    {
        $name = $request->name;

        if ($request->paginate) {
            $airports = Airport::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
            })->paginate($request->paginate);
        } else {
            $airports = Airport::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
            })->take($request->size)->get();
        }

        return $airports;
    }
}
