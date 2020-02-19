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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function index(){

        if(\Auth::user()->type=='company') {
            $tokens = OauthClient::where('company_user_id', \Auth::user()->company_user_id)->get();
        }else if(\Auth::user()->type=='admin') {
            $tokens = OauthClient::all();
        }
        return view('oauth.index',compact('tokens'));
    }

    public function createAccessToken(){

        $token = new OauthClient();

        $token->name="Password Grant Token ".str_random(5);
        $token->company_user_id=\Auth::user()->company_user_id;
        $token->secret=str_random(40);
        $token->redirect="http://localhost";
        $token->personal_access_client=0;
        $token->password_client=1;
        $token->revoked=0;

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
            'message' => 'Successfully created user!'], 201);
    }
    public function createToken(Request $request,$user_id)
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
        if(!Auth::attempt($credentials))
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

    public function deleteToken(Request $request,$id)
    {
        $token = OauthClient::find($id);
        $token->delete();

        return response()->json([
            'message' => 'Ok'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' =>
                                 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function test(){

        $client = new Client([
            'headers' => ['Content-Type'=>'application/json','Accept'=>'*/*'],
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
            $auth = json_decode( (string) $response->getBody() );
            //dd($auth->access_token);
            $response = $client->get('http://cargofive.dev.com/api/v1/quotes', [
                'headers' => [
                    'Content-Type'=>'application/json',
                    'X-Requested-With'=>'XMLHttpRequest',
                    'Authorization' => 'Bearer '.$auth->access_token,
                ]
            ]);

            $all = json_decode( (string) $response->getBody() );
            dd(json_encode($all));
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            echo "Unable to retrieve access token.";
        }
    }

    public function rates(Request $request)
    {
        if($request->size){
            $rates=Rate::whereHas('contract', function($q)  {
                $q->where('contracts.company_user_id',\Auth::user()->company_user_id);
            })->with('contract')->take($request->size)->get();
        }else{
            $rates=Rate::whereHas('contract', function($q)  {
                $q->where('contracts.company_user_id',\Auth::user()->company_user_id);
            })->with('contract')->get();
        }

        $collection = Collection::make($rates);
        $collection->transform(function ($rate) {
            $rate->origin_port=$rate->port_origin->code;
            $rate->destination_port=$rate->port_destiny->code;
            $rate->carrier_code=$rate->carrier->uncode;
            $rate->currency_code=$rate->currency->alphacode;
            $rate->rate_20=$rate->twuenty;
            $rate->rate_40=$rate->forty;
            $rate->rate_40_hc=$rate->fortyhc;
            $rate->rate_40_nor=$rate->fortynor;
            $rate->rate_45=$rate->fortyfive;
            $rate->valid_from=$rate->contract->validity;
            $rate->valid_until=$rate->contract->expire;
            $rate->contract_name=$rate->contract->name;
            $rate->contract_id=$rate->contract->id;
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

    public function charges(Request $request)
    {

        if($request->size){
            $charges=ViewLocalCharges::whereHas('contract',function ($q) {
                $q->where('company_user_id', \Auth::user()->company_user_id);
            })->take($request->size)->get();
        }else{
            $charges=ViewLocalCharges::whereHas('contract',function ($q) {
                $q->where('company_user_id', \Auth::user()->company_user_id);
            })->get();
        }

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            $charge->contract_name=$charge->contract->name;
            $charge->amount=$charge->ammount;
            $charge->currency_code=@$charge->currency;
            $charge->charge=$charge->surcharge;
            $charge->charge_type=$charge->changetype;
            if($charge->port_orig!=''){
                $explode_port = explode(',',$charge->port_orig);
                $origin_port = @$explode_port[1];
                $charge->origin_port=trim($origin_port," ");
            }
            if($charge->port_dest!=''){
                $explode_port = explode(',',$charge->port_dest);
                $destination_port = @$explode_port[1];
                $charge->destination_port=trim($destination_port," ");
            }
            if($charge->country_orig!=''){
                $charge->origin_country=$charge->country_orig;
            }
            if($charge->country_dest!=''){
                $charge->destination_country=$charge->country_dest;
            }
            $charge->carriers=$charge->carrier_uncode;
            $charge->valid_from=$charge->contract->validity;
            $charge->valid_until=$charge->contract->expire;
            unset($charge['carrier']);
            unset($charge['ammount']);
            unset($charge['currency']);
            unset($charge['carrier_uncode']);
            unset($charge['port_orig']);
            unset($charge['port_dest']);
            unset($charge['country_orig']);
            unset($charge['country_dest']);
            unset($charge['changetype']);
            unset($charge['surcharge']);
            unset($charge['contract']);
        });

        return $charges;
    }

    public function charges2(Request $request)
    {
        $charges=LocalCharPort::whereHas('localcharge',function ($q) {
            $q->whereHas('contract', function ($q){
                $q->where('company_user_id', \Auth::user()->company_user_id);
            });
        })->with('localcharge')->get();

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            $charge->id=$charge->localcharge->id;
            $charge->charge=$charge->localcharge->surcharge['name'];
            $charge->origin_port=$charge->portOrig->code;
            $charge->destination_port=$charge->portDest->code;
            $charge->charge_type=$charge->localcharge->typedestiny['description'];
            $charge->calculation_type=$charge->localcharge->calculationtype['name'];
            $charge->amount=$charge->localcharge->ammount;
            $charge->currency_code=$charge->localcharge->currency['alphacode'];
            $charge->contract=$charge->localcharge->contract['name'];
            $charge->contract_id=$charge->localcharge->contract['id'];
            $charge->valid_from=$charge->localcharge->contract['validity'];
            $charge->valid_until=$charge->localcharge->contract['expire'];
            unset($charge['portOrig']);
            unset($charge['portDest']);
            unset($charge['port_orig']);
            unset($charge['port_dest']);
            unset($charge['localcharge']);
            unset($charge['localcharge_id']);
        });

        return $charges;
    }

    public function chargesCountry(Request $request)
    {
        $charges=LocalCharCountry::whereHas('localcharge',function ($q) {
            $q->whereHas('contract', function ($q){
                $q->where('company_user_id', \Auth::user()->company_user_id);
            });
        })->with('localcharge')->get();

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            $charge->id=$charge->localcharge->id;
            $charge->charge=$charge->localcharge->surcharge['name'];
            $charge->origin_country=$charge->countryOrig->name;
            $charge->destination_country=$charge->countryDest->name;
            $charge->charge_type=$charge->localcharge->typedestiny['description'];
            $charge->calculation_type=$charge->localcharge->calculationtype['name'];
            $charge->amount=$charge->localcharge->ammount;
            $charge->currency_code=$charge->localcharge->currency['alphacode'];
            $charge->contract_name=$charge->localcharge->contract['name'];
            $charge->contract_id=$charge->localcharge->contract['id'];
            $charge->valid_from=$charge->localcharge->contract['validity'];
            $charge->valid_until=$charge->localcharge->contract['expire'];
            unset($charge['countryOrig']);
            unset($charge['countryDest']);
            unset($charge['country_orig']);
            unset($charge['country_dest']);
            unset($charge['localcharge']);
            unset($charge['localcharge_id']);
        });

        return $charges;
    }

    public function globalCharges(Request $request)
    {
        if($request->size){
            $charges=ViewGlobalCharge::where('company_user_id', \Auth::user()->company_user_id)->take($request->size)->get();   
        }else{
            $charges=ViewGlobalCharge::where('company_user_id', \Auth::user()->company_user_id)->get();
        }

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            if($charge->origin_port==null){
                unset($charge['origin_port']);
            }
            if($charge->destination_port==null){
                unset($charge['destination_port']);
            }
            if($charge->origin_country==null){
                unset($charge['origin_country']);
            }
            if($charge->destination_country==null){
                unset($charge['destination_country']);
            }
            unset($charge['carrier']);
            unset($charge['company_user_id']);
        });

        return $charges;
    }

    public function globalChargesCountry(Request $request)
    {
        $charges=GlobalCharCountry::whereHas('globalcharge',function ($q) {
            $q->where('company_user_id', \Auth::user()->company_user_id);
        })->with('globalcharge')->get();

        $collection = Collection::make($charges);
        $collection->transform(function ($charge) {
            $charge->id=$charge->globalcharge->id;
            $charge->charge=$charge->globalcharge->surcharge['name'];
            $charge->origin_country=$charge->countryOrig->name;
            $charge->destination_country=$charge->countryDest->name;
            $charge->charge_type=$charge->globalcharge->typedestiny['description'];
            $charge->calculation_type=$charge->globalcharge->calculationtype['name'];
            $charge->amount=$charge->globalcharge->ammount;
            $charge->currency_code=$charge->globalcharge->currency['alphacode'];
            $charge->carrier_code=$charge->globalcharge->carrier->carrier->uncode;
            $charge->valid_from=$charge->globalcharge->validity;
            $charge->valid_until=$charge->globalcharge->expire;
            unset($charge['countryOrig']);
            unset($charge['countryDest']);
            unset($charge['country_orig']);
            unset($charge['country_dest']);
            unset($charge['port_orig']);
            unset($charge['port_dest']);
            unset($charge['globalcharge']);
            unset($charge['globalcharge_id']);
            unset($charge['typedestiny_id']);
            unset($charge['globalCarrier']);
        });

        return $charges;
    }

    public function contracts(Request $request){
        if($request->size){
            $contracts = Contract::where('company_user_id','=',Auth::user()->company_user_id)->take($request->size)->get();
        }else{
            $contracts = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();
        }

        return $contracts;
    }
}
