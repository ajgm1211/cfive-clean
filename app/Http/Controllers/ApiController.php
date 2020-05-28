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
use App\GlobalCharge;
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
            })->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation')
                ->with('country')->paginate($request->paginate);
        } else {
            $ports = Harbor::when($name, function ($query, $name) {
                return $query->where('name', 'LIKE', '%' . $name . '%')->orWhere('code', 'LIKE', '%' . $name . '%');
            })->with('country')
                ->select('id', 'name', 'code', 'display_name', 'coordinates', 'country_id', 'varation as variation')->take($request->size)->get();
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

    public function searchV2($code_origin, $code_destination, $inicio, $fin, $api_company_id = 0)
    {
        try {
            return $this->processSearchV2($code_origin, $code_destination, $inicio, $fin, $api_company_id = 0);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while performing the operation'], 500);
        }
    }

    public function processSearchV2($code_origin, $code_destination, $inicio, $fin, $api_company_id = 0)
    {

        $portOrig = Harbor::where('code', $code_origin)->firstOrFail();

        $portDest = Harbor::where('code', $code_destination)->firstOrFail();;
        //Variables del usuario conectado


        $chargesOrigin = 'true';
        $chargesDestination = 'true';
        $chargesFreight = 'true';


        //Settings de la compañia
        $iduser = \Auth::user()->id;

        $companyId = User::find($iduser);
        $company = CompanyUser::where('id', $companyId->company_user_id)->first();

        $typeCurrency =  $company->currency->alphacode;
        $idCurrency = $company->currency_id;
        $company_user_id = $company->id;
        $user_id =  '';


        $companies = Company::where('api_id', '=', $api_company_id)->first();

        if (empty($companies)) {
            $companies_id = 0;
        } else {
            $companies_id = $companies->id;
        }

        $origin_port[] = $portOrig->id;
        $origin_country[] = $portOrig->country_id;

        $destiny_port[] = $portDest->id;
        $destiny_country[] = $portDest->country_id;

        $equipment = array('20', '40', '40HC', '40NOR', '45');

        $price_id = '';

        // Fecha Contrato

        $dateSince = $inicio;
        $dateUntil = $fin;

        //Colecciones
        $inlandDestiny = new collection();
        $inlandOrigin = new collection();

        //Markups Freight
        $freighPercentage = 0;
        $freighAmmount = 0;
        $freighMarkup = 0;
        // Markups Local
        $localPercentage = 0;
        $localAmmount = 0;
        $localMarkup = 0;
        $markupLocalCurre = 0;
        // Markups Local
        $inlandPercentage = 0;
        $inlandAmmount = 0;
        $inlandMarkup = 0;
        $markupInlandCurre = 0;


        // Calculo de los inlands
        $modality_inland = '1'; // FALTA AGREGAR EXPORT

        $company_inland = '1';
        $texto20 = 'Inland 20 x';
        $texto40 = 'Inland 40 x';
        $texto40hc = 'Inland 40HC x';

        // Consulta base de datos rates

        if ($companies_id != null || $companies_id != 0) {
            $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id) {
                $q->whereHas('contract_user_restriction', function ($a) use ($user_id) {
                    $a->where('user_id', '=', $user_id);
                })->orDoesntHave('contract_user_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $user_id, $company_user_id, $companies_id) {
                $q->whereHas('contract_company_restriction', function ($b) use ($companies_id) {
                    $b->where('company_id', '=', $companies_id);
                })->orDoesntHave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id) {
                $q->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id', '=', $company_user_id);
            })->with(['carrier' => function ($query) {
                $query->select('id', 'name', 'uncode', 'image');
            }]);
        } else {
            $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract')->whereHas('contract', function ($q) {
                $q->doesnthave('contract_user_restriction');
            })->whereHas('contract', function ($q) {
                $q->doesnthave('contract_company_restriction');
            })->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id) {
                $q->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil)->where('company_user_id', '=', $company_user_id);
            })->with(['carrier' => function ($query) {
                $query->select('id', 'name', 'uncode', 'image');
            }]);
        }




        // ************************* CONSULTA RATE API ******************************



        /*  if($chargesAPI != null){

      $client = new Client();

      foreach($origin_port as $orig){
        foreach($destiny_port as $dest){
          $response = $client->request('GET','http://cfive-api.eu-central-1.elasticbeanstalk.com/rates/HARIndex/'.$orig.'/'.$dest.'/'.trim($dateUntil));
        }
      }
      $arreglo2 = RateApi::whereIn('origin_port',$origin_port)->whereIn('destiny_port',$destiny_port)->with('port_origin','port_destiny','contract','carrier')->whereHas('contract', function($q) use($dateSince,$dateUntil,$company_user_id){
        $q->where('validity', '<=',$dateSince)->where('expire', '>=', $dateUntil);
      });
    }*/


        //ACA




        // Se agregan las condiciones para evitar traer rates con ceros dependiendo de lo seleccionado por el usuario

        /*    if(in_array('20',$equipment)){
      $arreglo->where('twuenty' , '!=' , "0");
    }
    if(in_array('40',$equipment)){
      $arreglo->where('forty' , '!=' , "0");
    }
    if(in_array('40HC',$equipment)){
      $arreglo->where('fortyhc' , '!=' , "0");
    }
    if(in_array('40NOR',$equipment)){
      $arreglo->where('fortynor' , '!=' , "0");
    }
    if(in_array('45',$equipment)){
      $arreglo->where('fortyfive' , '!=' , "0");
    }*/


        $arreglo = $arreglo->get();


        /*  if($chargesAPI != null){
      $arreglo2 = $arreglo2->get();
      $arreglo = $arreglo->merge($arreglo2);
    }
*/
        //$formulario = $request;
        $array20 = array('2', '4', '5', '6', '9', '10', '11'); // id  calculation type 2 = per 20 , 4= per teu , 5 per container
        $array40 =  array('1', '4', '5', '6', '9', '10', '11'); // id  calculation type 2 = per 40
        $array40Hc = array('3', '4', '5', '6', '9', '10', '11'); // id  calculation type 3 = per 40HC
        $array40Nor = array('7', '4', '5', '6', '9', '10', '11');  // id  calculation type 7 = per 40NOR
        $array45 = array('8', '4', '5', '6', '9', '10', '11');  // id  calculation type 8 = per 45

        $arrayContainers =  array('1', '2', '3', '4', '7', '8');

        $general = new collection();

        $collectionRate = new Collection();
        foreach ($arreglo as $data) {
            $contractStatus = $data->contract->status;

            $totalFreight = 0;
            $totalRates = 0;
            $totalT20 = 0;
            $totalT40 = 0;
            $totalT40hc = 0;
            $totalT40nor = 0;
            $totalT45 = 0;
            $totalT  = 0;
            //Variables Totalizadoras
            $totales = array();

            $tot_20_F = 0;
            $tot_40_F = 0;
            $tot_40hc_F = 0;
            $tot_40nor_F = 0;
            $tot_45_F = 0;

            $tot_20_O = 0;
            $tot_40_O = 0;
            $tot_40hc_O = 0;
            $tot_40nor_O = 0;
            $tot_45_O = 0;

            $tot_20_D = 0;
            $tot_40_D = 0;
            $tot_40hc_D = 0;
            $tot_40nor_D = 0;
            $tot_45_D = 0;

            $carrier[] = $data->carrier_id;
            $orig_port = array($data->origin_port);
            $dest_port = array($data->destiny_port);
            $rateDetail = new collection();
            $collectionOrigin = new collection();
            $collectionDestiny = new collection();
            $collectionFreight = new collection();


            $arregloRateTotal = array();
            $array_ocean_freight = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'currency' => $data->currency->alphacode);
            $arregloRate20 =  array();
            $arregloRate40 =  array();
            $arregloRate40hc =  array();
            $arregloRate40nor =  array();
            $arregloRate45 =  array();

            //Arreglos para guardar el rate

            $arregloRateSave['rate'] = array();
            $arregloRateSave['markups'] = array();

            //Arreglo para guardar charges
            $arregloCharges['origin'] =  array();

            $arregloOrigin =  array();
            $arregloFreight =  array();
            $arregloDestiny =  array();
            // globales
            $arregloOriginG =  array();
            $arregloFreightG =  array();
            $arregloDestinyG =  array();

            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);

            // Rates
            $equipment = array('20', '40', '40HC', '40NOR', '45');
            foreach ($equipment as $containers) {
                //Calculo para los diferentes tipos de contenedores
                if ($containers == '20') {
                    $markup20 = $this->freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $data->twuenty, $typeCurrency, $containers);

                    // dd($markup20);

                    $array20Detail = array('type' => '20DV', 'price' => $data->twuenty, 'currency' => $data->currency->alphacode);
                    $tot_20_F += $markup20['amount'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_20_save = array('c20' => $data->twuenty);
                    $arregloRateSave['rate']  = array_merge($array_20_save, $arregloRateSave['rate']);
                    // Markups
                    $array_20_markup =  array('m20' => $markup20['markup']);
                    $arregloRateSave['markups']  = array_merge($array_20_markup, $arregloRateSave['markups']);

                    $array20T = array_merge($array20Detail, $markup20);
                    $arregloRate20 = array_merge($array20T, $arregloRate20);
                    array_push($arregloRateTotal, $arregloRate20);
                    //Total
                    $totales['20F'] =  $tot_20_F;
                }
                if ($containers == '40') {
                    $markup40 = $this->freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $data->forty, $typeCurrency, $containers);
                    $array40Detail = array('type' => '40DV', 'price' => $data->forty, 'currency' => $data->currency->alphacode);
                    $tot_40_F += $markup40['amount']  / $rateC;
                    // Arreglos para guardar los rates
                    $array_40_save = array('c40' => $data->forty);
                    $arregloRateSave['rate']  = array_merge($array_40_save, $arregloRateSave['rate']);
                    // Markups
                    $array_40_markup =  array('m40' => $markup40['markup']);
                    $arregloRateSave['markups']  = array_merge($array_40_markup, $arregloRateSave['markups']);

                    $array40T = array_merge($array40Detail, $markup40);
                    $arregloRate40 = array_merge($array40T,  $arregloRate40);

                    $totales['40F'] = $tot_40_F;

                    array_push($arregloRateTotal, $arregloRate40);
                }
                if ($containers == '40HC') {
                    $markup40hc = $this->freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $data->fortyhc, $typeCurrency, $containers);
                    $array40hcDetail = array('type' => '40HC', 'price' => $data->fortyhc, 'currency' => $data->currency->alphacode);
                    $tot_40hc_F += $markup40hc['amount'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_40hc_save = array('c40hc' => $data->fortyhc);
                    $arregloRateSave['rate']  = array_merge($array_40hc_save, $arregloRateSave['rate']);
                    // Markups
                    $array_40hc_markup =  array('m40hc' => $markup40hc['markup']);
                    $arregloRateSave['markups']  = array_merge($array_40hc_markup, $arregloRateSave['markups']);

                    $array40hcT = array_merge($array40hcDetail, $markup40hc);
                    $arregloRate40hc = array_merge($array40hcT,  $arregloRate40hc);
                    $totales['40hcF'] = $tot_40hc_F;
                    array_push($arregloRateTotal, $arregloRate40hc);
                }
                if ($containers == '40NOR') {
                    $markup40nor = $this->freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $data->fortynor, $typeCurrency, $containers);
                    $array40norDetail = array('type' => '40NOR', 'price40' => $data->fortynor, 'currency' => $data->currency->alphacode);
                    $tot_40nor_F += $markup40nor['amount'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_40nor_save = array('c40nor' => $data->fortynor);
                    $arregloRateSave['rate']  = array_merge($array_40nor_save, $arregloRateSave['rate']);
                    // Markups
                    $array_40nor_markup =  array('m40nor' => $markup40nor['markup']);
                    $arregloRateSave['markups']  = array_merge($array_40nor_markup, $arregloRateSave['markups']);

                    $array40norT = array_merge($array40norDetail, $markup40nor);
                    $arregloRate40nor = array_merge($array40norT, $arregloRate40nor);
                    $totales['40norF'] = $tot_40nor_F;
                    array_push($arregloRateTotal, $arregloRate40nor);
                }
                if ($containers == '45') {
                    $markup45 = $this->freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $data->fortyfive, $typeCurrency, $containers);
                    $array45Detail = array('type' => '45HC', 'price' => $data->fortyfive, 'currency' => $data->currency->alphacode);
                    $tot_45_F += $markup45['amount'] / $rateC;
                    // Arreglos para guardar los rates
                    $array_45_save = array('c45' => $data->fortyfive);
                    $arregloRateSave['rate'] = array_merge($array_45_save, $arregloRateSave['rate']);
                    // Markups
                    $array_45_markup =  array('m45' => $markup45['markup']);
                    $arregloRateSave['markups']  = array_merge($array_45_markup, $arregloRateSave['markups']);

                    $array45T = array_merge($array45Detail, $markup45);
                    $arregloRate45 = array_merge($array45T,  $arregloRate45);
                    $totales['45F'] = $tot_45_F;
                    array_push($arregloRateTotal, $arregloRate45);
                }
            }

            // id de los port  ALL
            array_push($orig_port, 1485);
            array_push($dest_port, 1485);
            // id de los carrier ALL
            $carrier_all = 26;
            array_push($carrier, $carrier_all);
            // Id de los paises
            array_push($origin_country, 250);
            array_push($destiny_country, 250);

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
                })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm')->orderBy('typedestiny_id')->orderBy('calculationtype_id')->orderBy('surcharge_id')->get();
            } else {

                $localChar = LocalChargeApi::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                    $q->whereIn('carrier_id', $carrier);
                })->where(function ($query) use ($orig_port, $dest_port, $origin_country, $destiny_country) {
                    $query->whereHas('localcharports', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('localcharcountries', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'currency', 'surcharge.saleterm')->orderBy('typedestiny_id', 'calculationtype_id', 'surchage_id')->get();
            }


            foreach ($localChar as $local) {

                $rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);

                // Condicion para enviar los terminos de venta o compra
                if (isset($local->surcharge->saleterm->name)) {
                    $terminos = $local->surcharge->saleterm->name;
                } else {
                    $terminos = $local->surcharge->name;
                }

                $options = $local->surcharge->options;

                foreach ($local->localcharcarriers as $localCarrier) {
                    if ($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id ==  $carrier_all) {
                        //Origin
                        if ($chargesOrigin != null) {
                            if ($local->typedestiny_id == '1') {

                                if (in_array($local->calculationtype_id, $array20) && in_array('20', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '20DV', 'calculation_id' => $local->calculationtype->id, 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);

                                    $arregloOrigin = array_merge($arregloOrigin, $markup20);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_20_O  +=  $markup20['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40) && in_array('40', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);

                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloOrigin = array_merge($arregloOrigin, $markup40);
                                    $collectionOrigin->push($arregloOrigin);

                                    $tot_40_O  +=  $markup40['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);

                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloOrigin = array_merge($arregloOrigin, $markup40hc);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_40hc_O  +=   $markup40hc['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Nor) && in_array('40NOR', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40NOR', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloOrigin = array_merge($arregloOrigin, $markup40nor);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_40nor_O  +=  $markup40nor['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array45) && in_array('45', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '45HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloOrigin = array_merge($arregloOrigin, $markup45);
                                    $collectionOrigin->push($arregloOrigin);
                                    $tot_45_O  +=  $markup45['montoMarkup'];
                                }

                                if (in_array($local->calculationtype_id, $arrayContainers)) {
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container', 'type' => '99', 'calculation_id' => '5', 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'markupConvert' => 0.00);
                                } else {
                                    $arregloOrigin = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '99', 'calculation_id' => $local->calculationtype->id, 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'markupConvert' => 0.00);
                                }
                                $collectionOrigin->push($arregloOrigin);
                            }
                        }

                        //Destiny
                        if ($chargesDestination != null) {
                            if ($local->typedestiny_id == '2') {

                                if (in_array($local->calculationtype_id, $array20) && in_array('20', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '20DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloDestiny = array_merge($arregloDestiny, $markup20);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_20_D +=  $markup20['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40) && in_array('40', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloDestiny = array_merge($arregloDestiny, $markup40);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40_D  +=  $markup40['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloDestiny = array_merge($arregloDestiny, $markup40hc);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40hc_D  +=   $markup40hc['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Nor) && in_array('40NOR', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40NOR', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloDestiny = array_merge($arregloDestiny, $markup40nor);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_40nor_D  +=  $markup40nor['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array45) && in_array('45', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '45HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloDestiny = array_merge($arregloDestiny, $markup45);
                                    $collectionDestiny->push($arregloDestiny);
                                    $tot_45_D  +=  $markup45['montoMarkup'];
                                    $montoOrig = $local->ammount;
                                }

                                if (in_array($local->calculationtype_id, $arrayContainers)) {
                                    $arregloDestiny = array(
                                        'surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container', 'type' => '99', 'calculation_id' => '5', 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00
                                    );
                                } else {
                                    $arregloDestiny = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '99', 'calculation_id' => $local->calculationtype->id, 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                }
                                $collectionDestiny->push($arregloDestiny);
                            }
                        }
                        //Freight
                        if ($chargesFreight != null) {
                            if ($local->typedestiny_id == '3') {

                                if (in_array($local->calculationtype_id, $array20) && in_array('20', $equipment)) {

                                    $montoOrig = $local->ammount;

                                    $monto =   $local->ammount  / $rateMount;

                                    $monto = number_format($monto, 2, '.', '');
                                    $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '20DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloFreight = array_merge($arregloFreight, $markup20);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['20F'] += $markup20['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40) && in_array('40', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);

                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloFreight = array_merge($arregloFreight, $markup40);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40F'] +=  $markup40['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloFreight = array_merge($arregloFreight, $markup40hc);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40hcF'] +=   $markup40hc['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array40Nor)  && in_array('40NOR', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto = $local->ammount / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '40NOR', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloFreight = array_merge($arregloFreight, $markup40nor);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['40norF'] += $markup40nor['montoMarkup'];
                                }
                                if (in_array($local->calculationtype_id, $array45) && in_array('45', $equipment)) {

                                    $montoOrig = $local->ammount;
                                    $montoOrig = $this->perTeu($montoOrig, $local->calculationtype_id);
                                    $monto =   $local->ammount  / $rateMount;
                                    $monto = $this->perTeu($monto, $local->calculationtype_id);
                                    $monto = number_format($monto, 2, '.', '');
                                    $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $local->currency->id);
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '45HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency);
                                    $arregloFreight = array_merge($arregloFreight, $markup45);
                                    $collectionFreight->push($arregloFreight);
                                    $totales['45F'] +=  $markup45['montoMarkup'];
                                }

                                if (in_array($local->calculationtype_id, $arrayContainers)) {
                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => 'Per Container', 'type' => '99', 'calculation_id' => '5', 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                } else {

                                    $arregloFreight = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'type' => '99', 'calculation_id' => $local->calculationtype->id, 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $local->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                }

                                $collectionFreight->push($arregloFreight);
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
                    $query->whereHas('globalcharport', function ($q) use ($orig_port, $dest_port) {
                        $q->whereIn('port_orig', $orig_port)->whereIn('port_dest', $dest_port);
                    })->orwhereHas('globalcharcountry', function ($q) use ($origin_country, $destiny_country) {
                        $q->whereIn('country_orig', $origin_country)->whereIn('country_dest', $destiny_country);
                    });
                })->where('company_user_id', '=', $company_user_id)->with('globalcharport.portOrig', 'globalcharport.portDest', 'globalcharcarrier.carrier', 'currency', 'surcharge.saleterm')->get();


                foreach ($globalChar as $global) {

                    $rateMount = $this->ratesCurrency($global->currency->id, $typeCurrency);

                    // Condicion para enviar los terminos de venta o compra
                    if (isset($global->surcharge->saleterm->name)) {
                        $terminos = $global->surcharge->saleterm->name;
                    } else {
                        $terminos = $global->surcharge->name;
                    }

                    $options = $global->surcharge->options;

                    foreach ($global->globalcharcarrier as $globalCarrier) {
                        if ($globalCarrier->carrier_id == $data->carrier_id || $globalCarrier->carrier_id ==  $carrier_all) {
                            //Origin
                            if ($chargesOrigin != null) {
                                if ($global->typedestiny_id == '1') {

                                    if (in_array($global->calculationtype_id, $array20) && in_array('20', $equipment)) {

                                        $montoOrig = $global->ammount;
                                        $monto =   $global->ammount  / $rateMount;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '20DV', 'rate_id' => $data->id, 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloOriginG = array_merge($arregloOriginG, $markup20);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_20_O  +=  $markup20['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40) && in_array('40', $equipment)) {

                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40DV', 'rate_id' => $data->id, 'montoOrig' => $montoOrig,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloOriginG = array_merge($arregloOriginG, $markup40);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40_O  +=   $markup40['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {

                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloOriginG = array_merge($arregloOriginG, $markup40hc);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40hc_O  +=   $markup40hc['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Nor) && in_array('40NOR', $equipment)) {

                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40NOR', 'montoOrig' => $montoOrig,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloOriginG = array_merge($arregloOriginG, $markup40nor);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_40nor_O  +=  $markup40nor['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array45) && in_array('45', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '45HC', 'rate_id' => $data->id, 'montoOrig' => $montoOrig,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloOriginG = array_merge($arregloOriginG, $markup45);
                                        $collectionOrigin->push($arregloOriginG);
                                        $tot_45_O  +=  $markup45['montoMarkup'];
                                    }

                                    if (in_array($global->calculationtype_id, $arrayContainers)) {
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container', 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => '5', 'montoOrig' => 0.00,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    } else {
                                        $arregloOriginG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtype->id, 'montoOrig' => 0.00,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    }
                                    $collectionOrigin->push($arregloOriginG);
                                }
                            }
                            //Destiny
                            if ($chargesDestination != null) {
                                if ($global->typedestiny_id == '2') {

                                    if (in_array($global->calculationtype_id, $array20) &&  in_array('20', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $monto =   $global->ammount  / $rateMount;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '20DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloDestinyG = array_merge($arregloDestinyG, $markup20);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_20_D +=  $markup20['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40) && in_array('40', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloDestinyG = array_merge($arregloDestinyG, $markup40);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40_D  +=  $markup40['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloDestinyG = array_merge($arregloDestinyG, $markup40hc);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40hc_D  +=   $markup40hc['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Nor) && in_array('40NOR', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40NOR', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloDestinyG = array_merge($arregloDestinyG, $markup40nor);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_40nor_D  +=  $markup40nor['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array45) && in_array('45', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '45HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloDestinyG = array_merge($arregloDestinyG, $markup45);
                                        $collectionDestiny->push($arregloDestinyG);
                                        $tot_45_D  +=  $markup45['montoMarkup'];
                                    }


                                    if (in_array($global->calculationtype_id, $arrayContainers)) {
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container', 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => '5', 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    } else {
                                        $arregloDestinyG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtype->id, 'montoOrig' => 0.00,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    }

                                    $collectionDestiny->push($arregloDestinyG);
                                }
                            }
                            //Freight
                            if ($chargesFreight != null) {
                                if ($global->typedestiny_id == '3') {

                                    if (in_array($global->calculationtype_id, $array20) && in_array('20', $equipment)) {
                                        $montoOrig = $global->ammount;

                                        $monto =   $global->ammount  / $rateMount;

                                        $monto = number_format($monto, 2, '.', '');
                                        $markup20 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '20DV', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloFreightG = array_merge($arregloFreightG, $markup20);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['20F'] += $markup20['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40) && in_array('40', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;

                                        $monto = $this->perTeu($monto, $global->calculationtype_id);

                                        $monto = number_format($monto, 2, '.', '');

                                        $markup40 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);

                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40DV', 'rate_id' => $data->id, 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloFreightG = array_merge($arregloFreightG, $markup40);

                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40F'] +=  $markup40['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Hc) && in_array('40HC', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40hc = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40HC', 'rate_id' => $data->id, 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloFreightG = array_merge($arregloFreightG, $markup40hc);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40hcF'] +=   $markup40hc['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array40Nor) && in_array('40NOR', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup40nor = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '40NOR', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloFreightG = array_merge($arregloFreightG, $markup40nor);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['40norF'] += $markup40nor['montoMarkup'];
                                    }
                                    if (in_array($global->calculationtype_id, $array45) && in_array('45', $equipment)) {
                                        $montoOrig = $global->ammount;
                                        $montoOrig = $this->perTeu($montoOrig, $global->calculationtype_id);
                                        $monto =   $global->ammount  / $rateMount;
                                        $monto = $this->perTeu($monto, $global->calculationtype_id);
                                        $monto = number_format($monto, 2, '.', '');
                                        $markup45 = $this->localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $global->currency->id);
                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => $monto, 'currency' => $global->currency->alphacode, 'calculation_name' => $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '45HC', 'montoOrig' => $montoOrig, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency);
                                        $arregloFreightG = array_merge($arregloFreightG, $markup45);
                                        $collectionFreight->push($arregloFreightG);
                                        $totales['45F'] +=  $markup45['montoMarkup'];
                                    }

                                    if (in_array($global->calculationtype_id, $arrayContainers)) {

                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' => 'Per Container', 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => '5', 'montoOrig' => 0.00, 'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    } else {

                                        $arregloFreightG = array('surcharge_terms' => $terminos, 'surcharge_options' => json_decode($options), 'surcharge_id' => $global->surcharge->id, 'surcharge_name' => $global->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'currency' => $global->currency->alphacode, 'calculation_name' =>  $global->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $globalCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $global->calculationtype->id, 'montoOrig' => 0.00,  'currency_company' => $typeCurrency, 'currency_id' => $global->currency->id, 'currency_company_id' => $idCurrency, 'montoMarkupO' => 0.00, 'markupConvert' => 0.00);
                                    }

                                    $collectionFreight->push($arregloFreightG);
                                }
                            }
                        }
                    }
                }
            } // fin if contract Api*/
            // ############################ Fin global charges ######################


            // Ordenar las colecciones
            if (!empty($collectionFreight)) {
                //$collectionFreight = $this->OrdenarCollection($collectionFreight);
                $routes['ChargesFreight'] = $collectionFreight;
            }

            if (!empty($collectionDestiny)) {
                //$collectionDestiny = $this->OrdenarCollection($collectionDestiny);
                $routes['ChargesDestination'] = $collectionDestiny;
            }

            if (!empty($collectionOrigin)) {
                //dd($collectionOrigin);
                //$collectionOrigin = $this->OrdenarCollection($collectionOrigin);

                $routes['ChargesOrigin'] = $collectionOrigin;
            }




            // Totales Freight
            if (!isset($totales['20F']))
                $totales['20F'] = 0;
            if (!isset($totales['40F']))
                $totales['40F'] = 0;
            if (!isset($totales['40hcF']))
                $totales['40hcF'] = 0;
            if (!isset($totales['40norF']))
                $totales['40norF'] = 0;
            if (!isset($totales['45F']))
                $totales['45F'] = 0;



            $totalT20 = $tot_20_D +  $totales['20F'] + $tot_20_O;
            $totalT40  = $tot_40_D + $totales['40F'] + $tot_40_O;
            $totalT40hc  = $tot_40hc_D + $totales['40hcF'] + $tot_40hc_O;
            $totalT40nor  = $tot_40nor_D +  $totales['40norF'] + $tot_40nor_O;
            $totalT45  = $tot_45_D + $totales['45F'] + $tot_45_O;
            $totalT = $totales['20F'] + $totales['40F'] + $totales['40hcF'] +  $totales['40norF'] + $totales['45F'];
            $totalT = number_format($totalT, 2, '.', '');
            $totalRates += $totalT;
            $routes['ocean_freight'] = $array_ocean_freight;
            $routes['ocean_freight']['rates'] = $arregloRateTotal;
            $routes['origin_port'] = array('name' => $data->port_origin->name, 'code' => $data->port_origin->code);
            $routes['destination_port'] = array('name' => $data->port_destiny->name, 'code' => $data->port_destiny->code);
            $detalle['Rates'] = $routes;


            // SET ATRIBUTES
            $detalle['Rates']['transit_time'] = $data->transit_time;
            $detalle['Rates']['via'] = $data->via;
            $detalle['Rates']['schedule'] = @$data->scheduletype->name;

            //set carrier logo url
            $data->carrier['url'] = 'https://cargofive-production.s3.eu-central-1.amazonaws.com/imgcarrier/' . $data->carrier->image;
            $detalle['Rates']['carrier'] = $data->carrier;
            $detalle['Rates']['currency'] = $typeCurrency;
            $detalle['Rates']['total20'] =   number_format($totalT20, 2, '.', '');
            $detalle['Rates']['total40'] =  number_format($totalT40, 2, '.', '');
            $detalle['Rates']['total40HC'] =  number_format($totalT40hc, 2, '.', '');
            $detalle['Rates']['total40NOR'] = number_format($totalT40nor, 2, '.', '');
            $detalle['Rates']['total45'] =  number_format($totalT45, 2, '.', '');
            $detalle['Rates']['contract']['valid_from'] = $data->contract->validity;
            $detalle['Rates']['contract']['valid_to'] =   $data->contract->expire;
            $detalle['Rates']['contract']['number'] =   $data->contract->number;
            $detalle['Rates']['contract']['ref'] =   $data->contract->name;
            $detalle['Rates']['contract']['status'] =   $data->contract->status == 'publish' ? 'published' : $data->contract->status;

            $general->push($detalle);
        }


        $chargeOrigin = ($chargesOrigin != null) ? true : false;
        $chargeDestination = ($chargesDestination != null) ? true : false;
        $chargeFreight = ($chargesFreight != null) ? true : false;

        // Ordenar por prioridad
        if (in_array('20', $equipment))
            $arreglo  =  $arreglo->sortBy('total20');
        else if (in_array('40', $equipment))
            $arreglo  =  $arreglo->sortBy('total40');
        else if (in_array('40HC', $equipment))
            $arreglo  =  $arreglo->sortBy('total40hc');
        else if (in_array('40NOR', $equipment))
            $arreglo  =  $arreglo->sortBy('total40nor');
        else if (in_array('45', $equipment))
            $arreglo  =  $arreglo->sortBy('total45');

        return response()->json($general);
    }

    // Valor de la moneda configurada
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

    //Markups

    public function freightMarkups2($freighPercentage, $freighAmmount, $freighMarkup, $monto, $typeCurrency, $type)
    {

        if ($freighPercentage != 0) {
            $freighPercentage = intval($freighPercentage);
            $markup = ($monto *  $freighPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markup, "markupType" => "$typeCurrency ($freighPercentage%)", "amount" => $monto, 'amountMarkup' => $markup);
        } else {

            $markup = trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" => $markup, "markupConvert" => $freighMarkup, "markupType" => $typeCurrency, "amount" => $monto, 'amountMarkup' => $markup);
        }

        return $arraymarkup;
    }
    // Calculo TEU local y global charges
    public function perTeu($monto, $calculation_type)
    {
        if ($calculation_type == 4) {
            $monto = $monto * 2;
            return $monto;
        } else {
            return $monto;
        }
    }

    public function localMarkupsFCL($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $chargeCurrency)
    {

        if ($localPercentage != 0) {

            // Monto original
            $markupO = ($montoOrig *  $localPercentage) / 100;
            $montoOrig += $markupO;
            $montoOrig = number_format($montoOrig, 2, '.', '');

            $markup = ($monto *  $localPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupO, "markupType" => "$typeCurrency ($localPercentage%)", 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig);
        } else { // oki
            $valor = $this->ratesCurrency($chargeCurrency, $typeCurrency);

            if ($valor == '1')
                $markupOrig = $localMarkup * $valor;
            else
                $markupOrig = $localMarkup * $valor;

            $markup = trim($localMarkup);
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');

            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupOrig, "markupType" => $markupLocalCurre, 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig + $markupOrig);
        }


        return $arraymarkup;
    }
}
