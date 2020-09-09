<?php

namespace App\Http\Controllers;

use App\User;
use App\Providers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProvidersResource;



class ProvidersController extends Controller
{
    /**
     * Render index view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        return view('providers.index');
    }
    
    public function list(Request $request)
    {
        $results = Providers::filterByCurrentCompany()->filter($request);
        return ProvidersResource::collection($results);
    }
     /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function data(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;

        $providers = Providers::get()->map(function ($providers) {
            return $providers->only(['id', 'name','description']);
        });

        $users = User::whereHas('companyUser', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($company) {
            return $company->only(['id', 'name']);
        });

        $data = compact(
            'providers',    
            'users'
        );

        return response()->json(['data' => $data]);
    }
      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $company_user_id = Auth::user('web')->company_user_id;

        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            
        ]);

        $providers = providers::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'company_user_id' => $company_user_id,       
        ]);

        
        return new ProvidersResource($providers);
    }

    public function update(){

    }
    public function retrive(){

    }
    public function destroy(){

    }
    public function destroyAll(){

    }
    
}
