<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProviders;
use App\Http\Resources\ProvidersResource;
use App\Http\Resources\CarrierResource;
use App\Provider;
use App\ReferentialData;
use Illuminate\Validation\ValidationException;

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


       /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $results = Provider::filterByCurrentCompany()->filter($request);
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

        $providers = Provider::get()->map(function ($providers) {
            return $providers->only(['id', 'name','description','options']);
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
    public function store(StoreProviders $request)
    {
        $company_user_id = Auth::user('web')->company_user_id;

        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',    
        ]);

        $providers = Provider::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'company_user_id' => $company_user_id,
            'options' => [
                'generic'=>$request->generic==1 ? true : false
            ]       
        ]);
        
        $request->session()->flash('message.content', 'Register created successfully!');
        return new ProvidersResource($providers);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provider $providers)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $providers->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'options' => [
                'generic'=>$request->generic==1 ? true : false
            ] 
        ]);

        return new ProvidersResource($providers);
    }

    /**
     * Update the specified ref code in referential data.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function updateRefCode(Request $request, $id)
    {
        $vdata = $request->validate([
            'type' => 'required|in:provider,carrier',
            'ref_code' => 'required',
        ]);

        $class_name = str_replace(
            ['provider', 'carrier'], 
            ['App\Provider', 'App\Carrier'], 
            $vdata['type']
        );
        
        if($class_name == 'App\Provider'){
            $provider = $class_name::where(['id' => $id, 'company_user_id' => \Auth::user()->company_user_id])->firstOrFail();
        }else{
            $provider = $class_name::findOrFail($id);
        }
        
        $json_data = json_encode([ 'ref_code' => $vdata['ref_code'] ]);

        ReferentialData::updateOrCreate(
            [ 
                'referential_id' => $id, 
                'referential_type' => $class_name, 
                'company_user_id' => \Auth::user()->company_user_id
            ],
            [ 
                'user_id' => \Auth::user()->id, 
                'json_data' => $json_data
            ] 
        );
        
        if($vdata['type'] == 'carrier')
            return (new CarrierResource($provider))->companyUser(\Auth::user()->companyUser);

        return new ProvidersResource($provider);
    }


    
        /**
     * Duplicate the specified resource.
     *
     * @param  \App\Provider  $providers)
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Provider $providers)
    {
        $new_provider = $providers->duplicate();

        return new ProvidersResource($new_provider);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $providers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provider $providers)
    {
        $providers->delete();

        return response()->json(null, 204);
    }


    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('providers')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }

}
