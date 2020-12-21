<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\BrowserTrait;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Intercom\IntercomClient;

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
    use BrowserTrait;

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
    }

    /*  public function userCrisp($user)
    {

    //Evento Crisp
    $CrispClient = new EventCrisp();
    $exist =  $CrispClient->checkIfExist($user->email);
    if ($exist != 'true') { //Creamos el perfil
    $params = array('email' => $user->email, 'person' => array('nickname' => $user->name . " " . $user->lastname));
    if ($user->company_user_id != '') {
    $params['company'] = array('name' => $user->companyUser->name);
    }
    $people = $CrispClient->createProfile($params);
    } else { //validamos que tenga compañia si no lo actualizamos
    $people = $CrispClient->findByEmail($user->email);
    if (isset($people['company']['name'])) {
    $params = array('company' => array('name' => $user->companyUser->name));
    $people = $CrispClient->updateProfile($params, $user->email);
    }
    }
    }
     */

    public function intercom($client, $user)
    {

        $cliente = $client->users->getUsers(["email" => $user->email]);
        if ($cliente->total_count > 1) {
            foreach ($cliente->users as $u) {
                if ($u->type == "user") {
                    if ($u->user_id != $user->id) {
                        try{

                            $client->users->archiveUser($u->id);
                      
                          } catch (\Intercom\Exception\IntercomException $e) {
                            \Log::error("Ocurrio un  error intercom con el siguiente usuario".$u->email);
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

        return redirect()->intended($this->redirectPath());
    }
}
