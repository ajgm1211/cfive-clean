<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Carrier;
use App\GroupContainer;
use App\Direction;
use App\Container;
use App\Harbor;
use App\Currency;
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
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {        
        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });
        $equipments = GroupContainer::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });
        $directions = Direction::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'name']);
        });

        $currencies = Currency::get()->map(function ($currency) {
            return $currency->only(['id', 'alphacode']);
        });

        $containers = Container::get();

        $data = [
            'carriers' => $carriers,
            'equipments' => $equipments,
            'directions' => $directions,
            'containers' => $containers,
            'currencies' => $currencies,
            'harbors' => $harbors
 
        ];

        return response()->json(['data' => $data ]);
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
            'direction' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'status' => 'required',
            'remarks' => 'sometimes',
            'gp_container' => 'required',
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
            'gp_container_id' => $data['gp_container'],
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
    public function edit(Request $request, Contract $contract)
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
            'direction' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'remarks' => 'present',
            'gp_container' => 'required',
            'carriers' => 'required'
        ]);
        
        $contract->update([
            'name' => $data['name'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'remarks' => $data['remarks'],
            'gp_container_id' => $data['gp_container'],
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return response()->json(null, 204);
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
