<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\VerifyUser;
use App\Mail\VerifyMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\SlackNotification;
class RegisterController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

  use RegistersUsers;

  /**
     * Where to redirect users after registration.
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
    $this->middleware('guest');
  }

  /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
  protected function validator(array $data)
  {
 /*   $validation = Validator::make( $data, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:2|confirmed',
      'g-recaptcha-response' => 'required|recaptcha',
    ]);

    // redirect on validation error
    if ( $validation->fails() ) {
      // change below as required
      return redirect('/login')->with('status', 'We ');
    }else{
      
      return $validation;
    }
    */

    return Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:2|confirmed',
      'g-recaptcha-response' => 'required|recaptcha',
    ]);
  }

  /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
  protected function create(array $data)
  {

    $user = User::create([
      'name' => $data['name'],
      'lastname' => $data['lastname'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'password' => Hash::make($data['password'])


    ]);
    //aaaaa@ccccc.com

    $user->assignRole('company');

    $message = $user->name." ".$user->lastname." has been registered in Cargofive." ;
    $user->notify(new SlackNotification($message));

    VerifyUser::create([
      'user_id' => $user->id,
      'token' => str_random(40)
    ]);

    \Mail::to($user->email)->send(new VerifyMail($user));
    return $user;
  }

  /**
     * Verify email user after register
     *
     * @param  string  $token
     * @return view
     */
  public function verifyUser($token)
  {
    $verifyUser = VerifyUser::where('token', $token)->first();
    if(isset($verifyUser) ){
      $user = $verifyUser->user;
      if(!$user->verified) {
        $verifyUser->user->verified = 1;
        $verifyUser->user->save();
        VerifyUser::where('token', $token)->delete();
        $status = "Your e-mail is verified. You can now login.";
      }else{
        $status = "Your e-mail is already verified. You can now login.";
      }
    }else{
      //  return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
    }

    //   return redirect('/login')->with('status', $status);
  }

  // @overwrite
  protected function registered(Request $request, $user)
  {


    $this->guard()->logout();


    return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
  }
}
