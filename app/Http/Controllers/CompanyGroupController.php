<?php

namespace App\Http\Controllers;

use App\CompanyGroup;
use App\Company;
use App\PriceLevelGroup;
use App\CompanyGroupDetail;
use App\Http\Resources\CompanyGroupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('companygroup.index');
    }

    public function list(Request $request)
    {
        $results = CompanyGroup::filterByCurrentCompany()->filter($request);

        return CompanyGroupResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price_level' => 'required',
            'companies' => 'required',
        ]);

        $company_user_id = \Auth::user()->company_user_id;

        $company_group = CompanyGroup::create([
            'name' => $data['name'],
            'status' => true,
            'company_user_id' => $company_user_id,  
        ]);
        
        $price_level_group = new PriceLevelGroup();
        $price_level_group->price_level_id = $data['price_level']['id'];
        $price_level_group->group()->associate($company_group)->save();

        foreach($data['companies'] as $company_req){
            $company = Company::where('id', $company_req['id'])->first();

            $company_group_details = new CompanyGroupDetail;
            $company_group_details->company_id = $company->id;
            $company_group_details->company_group()->associate($company_group)->save();
        }

        return new CompanyGroupResource($company_group);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyGroup  $company_group
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyGroup $company_group)
    {
        return view('companygroup.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyGroup  $company_group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyGroup $company_group)
    {
        $data = $request->validate([
            'name' => 'required',
            'status' => 'required',
            'companies' => 'sometimes',
        ]);

        $company_group->update([
            'name' => $data['name'],
            'status' => $data['status'],
        ]);

        if(array_key_exists('companies',$data)){
            $company_group_details = CompanyGroupDetail::where('company_group_id',$company_group->id)->get();

            foreach($company_group_details as $detail){
                foreach($data['companies'] as $company_req){
                    if(){
                        CompanyGroupDetail::create([
                            'company_group_id' => $company_group->id,
                            'company_id' => $company_req['id'],
                        ]);
                    }elseif(){
        
                    }
                }

            }

                

        }

        return new CompanyGroupResource($company_group);
    }

    /**
     * Clone the specified resource in storage.
     *
     * @param  \App\CompanyGroup  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function duplicate(CompanyGroup $company_group)
    {
        $new_company_group = $company_group->duplicate();

        return new CompanyGroupResource($new_company_group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyGroup  $company_group
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyGroup $company_group)
    {
        $company_group->delete();

        return response()->json(['message' => 'Ok']);
    }

    /**
     * Mass remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        $toDestroy = CompanyGroup::whereIn('id', $request->input('ids'))->get();

        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }
}
