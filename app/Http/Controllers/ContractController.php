<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContractResource;

class ContractController extends Controller
{
    /**
     * Render index view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	return view('contract.index');
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        
        $results = Contract::filterByCurrentCompany()->filter($request);

    	return ContractResource::collection($results);
    }

    /**
     * Render create view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('contract.create');
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
            'direction' => 'required'
            'validity' => 'required',
            'expire' => 'required',
            'status' => 'required',
            'remarks' => 'sometimes',
            'carriers' => 'required'
        ]);

        $contract = Contract::create([
            'name' => $data['name'],
            'number' => null,
            'company_user_id' => $company_user_id,
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $data['status'],
            'remarks' => $data['remarks']
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);
    }

    /**
     * Render edit view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        return view('contract.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'name' => 'required',
            'direction' => 'required'
            'validity' => 'required',
            'expire' => 'required',
            'status' => 'required',
            'remarks' => 'present',
            'carriers' => 'required'
        ]);
        
        $contract->update([
            'name' => $data['name'],
            'number' => null,
            'company_user_id' => $company_user_id,
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $data['status'],
            'remarks' => $data['remarks']
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Contract $contract)
    {
        return new ContractResource($contract, true);
    }
   
}
