<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Intercom\IntercomClient;
use App\Http\Traits\BrowserTrait;

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

  // @overwrite
  public function authenticated(Request $request, $user)
  {  
    $client = new IntercomClient('dG9rOmVmN2IwNzI1XzgwMmFfNDdlZl84NzUxX2JlOGY5NTg4NGIxYjoxOjA=', null, ['Intercom-Version' => '1.1']);
    $client->users->create([
      "email" => $user->email,
      "user_id" => $user->id,
      "name" => $user->name,
    ]);
    // Crear hash id del usuario logueado 


    if($user->company_user_id != ""){
      setHashID();
      $client->users->create([
        "email" => $user->email,
        "companies" => [
          [
            "company_id" => $user->company_user_id,
          ]
        ]
      ]);
    }

    $browser = $this->getBrowser();

    if($browser["name"]=="Internet Explorer"){
      auth()->logout();
      return back()->with('warning', 'This site is not compatible with Internet Explorer. Please try signing in with Google Chrome, Firefox or other');   
    }

    if (!$user->verified) {
      auth()->logout();
      return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
    }else if($user->state!=1){
      auth()->logout();
      return back()->with('warning', 'This user has been disabled.');
    }else  if($user->company_user_id==''){
      return redirect('/settings');
    }

    return redirect()->intended($this->redirectPath());
  }
}
