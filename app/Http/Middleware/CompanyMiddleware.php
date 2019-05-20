<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CompanyMiddleware
{
  /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
  public function handle($request, Closure $next)
  {
    if(isset($request->route()->parameters['user'])){

      $idUser = $request->route()->parameters['user'];
      $user = User::find($idUser);    
  
      if($user != null){
        if( Auth::user()->company_user_id !='admin'){
          if($user->company_user_id != Auth::user()->company_user_id){

            abort(403, "You can not perform an action on a user that does not belong to your company");
          }

        }
      }
    }

    return $next($request);
  }
}
