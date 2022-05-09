<?php

namespace App\Http\Controllers;

use App\CalculationTypeLcl;
use App\Carrier;
use App\Company;
use App\ContractLcl;
use App\Country;
use App\Currency;
use App\Direction;
use App\Harbor;
use App\Helpers\HelperAll;
use App\Http\Resources\ContractLclResource;
use App\NewContractRequestLcl;
use App\Surcharge;
use App\TypeDestiny;
use App\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractLclController extends Controller
{
    /**
     * Render index view
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('contract_lcl.index');
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function list(Request $request)
    {
        $results = ContractLcl::filterByCurrentCompany()->filter($request);

        return ContractLclResource::collection($results);
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $company_user_id = Auth::user()->company_user_id;

        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });

        $directions = Direction::get()->map(function ($direction) {
            return $direction->only(['id', 'name']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $currencies = Currency::get()->map(function ($currency) {
            return $currency->only(['id', 'alphacode']);
        });

        $countries = Country::get()->map(function ($country) {
            $country['display_name'] = $country['name'];
            return $country->only(['id', 'display_name', 'name']);
        });

        $surcharges = Surcharge::where('company_user_id', '=', $company_user_id)->get()->map(function ($surcharge) {
            return $surcharge->only(['id', 'name']);
        });

        $calculation_types = CalculationTypeLcl::where('options->type','!=','rate_only')->get()->map(function ($calculation) {
            return $calculation->only(['id', 'name']);
        });

        $destination_types = TypeDestiny::get()->map(function ($destination_type) {
            return $destination_type->only(['id', 'description']);
        });

        $companies = Company::where('company_user_id', '=', $company_user_id)->get()->map(function ($company) {
            return $company->only(['id', 'business_name']);
        });

        $users = User::whereHas('companyUser', function ($q) use ($company_user_id) {
            $q->where('company_user_id', '=', $company_user_id);
        })->get()->map(function ($company) {
            return $company->only(['id', 'name']);
        });

        //Roles
        $user = User::find(Auth::user()->id);
        $rol = $user->getRoleNames()->first();

        $data = compact(
            'carriers',
            'directions',
            'currencies',
            'harbors',
            'surcharges',
            'countries',
            'calculation_types',
            'destination_types',
            'companies',
            'rol',
            'users'
        );

        return response()->json(['data' => $data]);
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function surcharge_data(Request $request, ContractLcl $contract)
    {
        $rates = $contract->rates;
        $all_harbor_row = Harbor::find(1485);
        $all_country_row = Country::find(250);

        $origin_harbors = $rates->pluck('port_origin')->push($all_harbor_row);
        $destiny_harbors = $rates->pluck('port_destiny')->push($all_harbor_row);

        $ori_harbors = $origin_harbors->unique('id')->values();
        $des_harbors = $destiny_harbors->unique('id')->values();

        $ori_countries = $origin_harbors->map(function ($harbor) {
            $country = ['id' => $harbor->country->id, 'display_name' => $harbor->country->name];
            return $country;
        })->unique('id')->values();

        $des_countries = $destiny_harbors->map(function ($harbor) {
            $country = ['id' => $harbor->country->id, 'display_name' => $harbor->country->name];
            return $country;
        })->unique('id')->values();

        $data = compact(
            'ori_harbors',
            'des_harbors',
            'ori_countries',
            'des_countries'
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
            'direction' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'carriers' => 'required',
        ]);

        $contract = ContractLcl::create([
            'name' => $data['name'],
            'number' => null,
            'company_user_id' => $company_user_id,
            'user_id' => Auth::user()->id,
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => 'publish',
            'comments' => '',
            'is_manual' => 1
        ]);

        $contract->createCustomCode();
        $contract->ContractCarrierSync($data['carriers']);

        return new ContractLclResource($contract);
    }

    /**
     * Render edit view
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ContractLcl $contract)
    {
        return view('contract_lcl.edit');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(ContractLcl $contract)
    {
        return new ContractLclResource($contract);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function duplicate(ContractLcl $contract)
    {
        $new_contract = $contract->duplicate();
        $new_contract->update([
            'user_id' => Auth::user()->id,
        ]);

        return new ContractLclResource($new_contract);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractLcl $contract)
    {
        $status_erased = 1;
        if ($contract->status == 'incomplete') {

            $requestContract = NewContractRequestLcl::where('contract_id', $contract->id);
            if (empty($requestContract) == 0) {

                $requestContract->update(['erased_contract' => $status_erased]);
            }
        }

        $contract->contract_code = null;
        $contract->update();
        $contract->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        ContractLcl::whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified media resource.
     *
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function removefile(Request $request, Contractlcl $contract)
    {
        $media = $contract->getMedia('document')->where('id', $request->input('id'))->first();

        if (!empty($media) == 0) {
            $media->delete();
        }

        return response()->json(null, 204);
    }

    /**
     * Get all files from the contract model resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFiles(Request $request, ContractLcl $contract)
    {
        $contract_media = $contract->getMedia('document')->map(function ($media, $key) {
            return [
                'id' => $media->id,
                'name' => substr($media->name, 14),
                'size' => $media->size,
                'type' => $media->mime_type,
                'url' => $media->getFullUrl(),
            ];
        });

        return response()->json(['data' => $contract_media]);
    }

    /**
     * Store media to an specified model contract.
     *
     * @param  use \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function storeMedia(Request $request, ContractLcl $contract)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $fileName = HelperAll::removeAccent($name);

        $file->move($path, $fileName);

        $media = $contract->addMedia(storage_path('tmp/uploads/' . $fileName))->addCustomHeaders([
            'ACL' => 'public-read',
        ])->toMediaCollection('document', 'contracts3');

        return response()->json([
            'contract' => new ContractLclResource($contract),
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'url' => $media->getFullUrl(),
        ]);
    }

    /**
     * Update the specified resource of Contract Restriction.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function updateRestrictions(Request $request, ContractLcl $contract)
    {
        $data = $request->validate([
            'companies' => 'sometimes',
            'users' => 'sometimes',
        ]);

        $contract->ContractCompaniesRestrictionsSync($data['companies'] ?? []);
        $contract->ContractUsersRestrictionsSync($data['users'] ?? []);

        return new ContractLclResource($contract);
    }

    /**
     * Update the specified resource of Contract Remarks.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function updateRemarks(Request $request, ContractLcl $contract)
    {
        $data = $request->validate([
            'remarks' => 'sometimes',
        ]);

        $contract->update(['comments' => @$data['remarks']]);

        return new ContractLclResource($contract);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContractLcl $contract)
    {
        $data = $request->validate([
            'name' => 'required',
            'direction' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'carriers' => 'required',
        ]);

        $status = $this->updateStatus($contract, $data);

        $contract->update([
            'name' => $data['name'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $status,
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractLclResource($contract);
    }

    public function updateStatus($contract, $data)
    {

        $date = date('Y-m-d');
        $expire = date('Y-m-d', strtotime($data['expire']));

        if ($contract->status != 'incomplete') {
            if ($date <= $expire) {
                $status = 'publish';
            } else {
                $status = 'expired';
            }
        } else {
            $status = 'incomplete';
        }

        return $status;
    }
}
