<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\BrowserTrait;
use App\User;
use App\OauthClient;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Intercom\IntercomClient;
use GeneaLabs\LaravelMixpanel\LaravelMixpanel;
use App\Http\Traits\MixPanelTrait;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;  

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;
    use BrowserTrait, MixPanelTrait;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        parent::__construct();
    }


    public function intercom($client, $user)
    {

        $cliente = $client->users->getUsers(["email" => $user->email]);
        if ($cliente->total_count > 1) {
            foreach ($cliente->users as $u) {
                if ($u->type == "user") {
                    if ($u->user_id != $user->id) {
                        try {

                            $client->users->archiveUser($u->id);
                        } catch (\Intercom\Exception\IntercomException $e) {
                            \Log::error("Ocurrio un  error intercom con el siguiente usuario" . $u->email);
                            return false;
                        }
                    }
                }
            }
        }
    }

    public function updateKey($user)
    {

        if ($user->people_key == "") {
            $usuario = User::find($user->id);
            $uuid = \Uuid::generate(4);
            $usuario->people_key = $uuid->string;
            $usuario->update();
        }
    }

    // @overwrite
    public function authenticated(Request $request, $user)
    {
        $client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.4']);
        $this->intercom($client, $user);

        $cliente = $client->users->getUsers(["email" => $user->email]);
        if ($cliente->total_count == 0) {

            $client->users->create([
                "email" => $user->email,
                "user_id" => $user->id,
                "name" => $user->name,
            ]);
            // Crear hash id del usuario logueado
            if ($user->company_user_id != "") {
                setHashID();
                //$this->intercom($client,$user);
                $client->users->create([
                    "email" => $user->email,
                    "companies" => [
                        [
                            "name" => $user->companyUser->name,
                            "company_id" => $user->company_user_id,
                        ],
                    ],
                ]);
            }
        }

        $browser = $this->getBrowser();
        //Fin evento

        if ($browser["name"] == "Internet Explorer") {
            auth()->logout();
            return back()->with('warning', 'This site is not compatible with Internet Explorer. Please try signing in with Google Chrome, Firefox or other');
        }

        if (!$user->verified) {
            auth()->logout();
            return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        } else if ($user->state != 1) {
            auth()->logout();
            return back()->with('warning', 'This user has been disabled.');
        } else if (env('APP_VIEW') == 'operaciones' && ($user->hasRole('company') || $user->hasRole('subuser'))) {
            auth()->logout();
            return back()->with('warning', 'This user does not have administrator permission.');
        } else if ($user->company_user_id == '') {
            return redirect('/settings');
        }

        $this->storeApiToken($request->input(), $user);

        /** Tracking login event with Mix Panel*/
        $this->trackEvents("login", $user);

        return redirect()->intended($this->redirectPath());
    }

    public function storeApiToken($loginData, $user)
    {
        $oauth_client = OauthClient::where('user_id',$user->id)->first();
        
        if($oauth_client != null){
            $username = $loginData['email'];
            $password = $loginData['password'];
            $user_id = $user->id;
        }else{
            $oauth_client = OauthClient::where('user_id',1)->first();
            $username = 'info@cargofive.com';
            $password = 'gencomex18';
            $user_id = 1;
        }
        
        $user_secret = $oauth_client->secret; 
        $app_url = $this->customEnv['appUrl'] . "/oauth/token";

        $client = new \GuzzleHttp\Client([              
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
           ]);
                // Create a POST request
            $response = $client->request(
                'POST',
                $app_url,
                [
                    'json' => [
                        'grant_type' => 'password',
                        'username' =>  $username,
                        'password' => $password,
                        'client_secret'=> $user_secret ,
                        'client_id' => $user_id,
                    ]
                ]
            );

        $body = $response->getBody()->getContents();

        $token = json_decode($body,true);
        $bearer_token = 'Bearer ' . $token['access_token'];

        $user->update(['api_token' => $bearer_token]);
    }
}
