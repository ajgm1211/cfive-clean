<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\OauthClient;
use App\User;
use App\Rate;
use App\Contract;
use App\OauthAccessToken;
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
        $rates=Rate::whereHas('contract', function($q)  {
            $q->where('contracts.company_user_id',\Auth::user()->company_user_id);
        })->with('contract')->get();

        //$contracts = DB::table('rates')->join('contracts', 'contracts.id', '=', 'rates.contract_id')->get();
        $array = new Collection();
        $collection = Collection::make($rates);
        $collection->transform(function ($rate) {
            $rate->origin_port=$rate->port_origin->display_name;
            $rate->destination_port=$rate->port_destiny->display_name;
            $rate->carrier_code=$rate->carrier->uncode;
            $rate->currency_code=$rate->currency->alphacode;
            $rate->rate_20=$rate->twuenty;
            $rate->rate_40=$rate->forty;
            $rate->rate_40_hc=$rate->fortyhc;
            $rate->rate_40_nor=$rate->fortynor;
            $rate->rate_45=$rate->fortyfive;
            $rate->validity=$rate->contract->validity;
            $rate->contract_name=$rate->contract->name;
            unset($rate['id']);
            unset($rate['port_origin']);
            unset($rate['destiny_port']);
            unset($rate['port_destiny']);
            unset($rate['contract']);
            unset($rate['contract_id']);
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
}
