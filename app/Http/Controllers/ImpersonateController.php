<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class ImpersonateController extends Controller
{
    /** 
     * Create a new controller instance. 
     * 
     * @return void 
     */ 
    public function __construct() 
    { 
        $this->middleware('auth');
        // $this->middleware('can:impersonate');
    }

    /**
     * Impersonate the given user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function impersonate(User $user)
    {
        if ($user->id !== ($original = \Auth::user()->id) && $user->type!=='admin') {
            session()->put('original_user', $original);
            session()->put('impersonate', 1);
            
            //Verify if user have a company_user associated
            if($user->company_user_id!=''){
                auth()->login($user);
            }else{
                return redirect('/users/home');
            }
        }

        return redirect('/home');
    }

    /**
     * Revert to the original user.
     *
     * @return \Illuminate\Http\Response
     */
    public function revert()
    {
        auth()->loginUsingId(session()->get('original_user'));

        session()->forget('original_user');
        session()->forget('impersonate');

        return redirect('/users/home');
    }
}
