<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Traits\BrowserTrait;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    }else if(env('APP_VIEW') == 'operaciones' && !$user->hasRole('administrator')){
      auth()->logout();
      return back()->with('warning', 'This user does not have administrator permission.');
    }else  if($user->company_user_id==''){
      return redirect('/settings');
        
    }

    return redirect()->intended($this->redirectPath());
  }
}
