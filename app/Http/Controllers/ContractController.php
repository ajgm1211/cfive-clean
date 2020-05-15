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
use App\Surcharge;
use App\CalculationType;
use App\TypeDestiny;
use App\Country;
use App\Company;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContractResource;
use Illuminate\Support\Facades\DB;

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
        $company_user_id = \Auth::user()->company_user_id;

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

        $countries = Country::get()->map(function ($country) {
            return $country->only(['id', 'name']);
        });

        $surcharges = Surcharge::where('company_user_id', '=' , $company_user_id)->get()->map(function ($surcharge) {
            return $surcharge->only(['id', 'name']);
        });

        $calculation_types = CalculationType::get()->map(function ($calculation) {
            return $calculation->only(['id', 'name']);
        });

        $destination_types = TypeDestiny::get()->map(function ($destination_type) {
            return $destination_type->only(['id', 'description']);
        });

        $companies = Company::where('company_user_id', '=', $company_user_id)->get()->map(function ($company) {
            return $company->only(['id', 'business_name']);
        });

        $users = User::whereHas('companyUser', function($q) use ($company_user_id) { 
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($company) {
            return $company->only(['id', 'name']);
        });

        $containers = Container::get();

        $data = compact(
            'carriers',
            'equipments',
            'directions',
            'containers',
            'currencies',
            'harbors',
            'surcharges',
            'countries',
            'calculation_types',
            'destination_types',
            'companies',
            'users');

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
            'status' => 'publish',
            'gp_container_id' => $data['gp_container'],
            'remarks' => ''
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
            'gp_container' => 'required',
            'carriers' => 'required'
        ]);
        
        $contract->update([
            'name' => $data['name'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'remarks' => '',
            'gp_container_id' => $data['gp_container'],
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);   
    }

    /**
     * Update the specified resource of Contract Restriction.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function updateRestrictions(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'companies' => 'sometimes',
            'users' => 'sometimes'
        ]);
        
        $contract->ContractCompaniesRestrictionsSync($data['companies'] ?? []);
        $contract->ContractUsersRestrictionsSync($data['users'] ?? []);

        return new ContractResource($contract);   
    }


    /**
     * Update the specified resource of Contract Remarks.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function updateRemarks(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'remarks' => 'sometimes'
        ]);
        
        $contract->update(['remarks' => $data['remarks']]);

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

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Contract $contract)
    {
        $new_contract = $contract->duplicate(); 

        return new ContractResource($new_contract, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  use Spatie\Permission\Models\FCLSurcharge  $fclsurcharge
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('contracts')->whereIn('id', $request->input('ids'))->delete(); 

        return response()->json(null, 204);
    }
   
}
