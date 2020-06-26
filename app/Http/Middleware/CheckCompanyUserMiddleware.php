<?php

namespace App\Http\Middleware;

use Closure;
use App\Contract;
use Illuminate\Support\Facades\Auth;

class CheckCompanyUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $key)
    {

        $model = $request->{$key};

        if($model->company_user_id != $request->user()->company_user_id){

            if($request->ajax() || $request->wantsJson())
                abort(403, 'Unauthorized action.');
            else
                return redirect()->route('quotes-v2.search'); 

        }

        return $next($request);

    }
}
