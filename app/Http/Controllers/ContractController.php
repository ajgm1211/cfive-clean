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
use App\Jobs\NotificationsJob;
use App\Jobs\ProcessContractFile;
use App\NewContractRequest;
use App\Notifications\N_general;
use App\Notifications\SlackNotification;
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

        $equipments = GroupContainer::get()->map(function ($equipment) {
            return $equipment->only(['id', 'name']);
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

        $calculation_types = CalculationType::get()->map(function ($calculation) {
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
        return new ContractResource($contract);
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

        return new ContractResource($new_contract);
    }

    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('contracts')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified media resource.
     *
     * @param  \App\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function removefile(Request $request, Contract $contract)
    {
        $media = $contract->getMedia('document')->where('id', $request->input('id'))->first();
        $media->delete();

        return response()->json(null, 204);
    }

    /**
     * Get all files from the contract model resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFiles(Request $request, Contract $contract)
    {
        $contract_media = $contract->getMedia('document')->map(function ($media, $key) {
            return [
                'id' => $media->id,
                'name' => substr($media->name, 14),
                'size' => $media->size,
                'type' => $media->mime_type,
                'url' => $media->getFullUrl()
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
    public function storeMedia(Request $request, Contract $contract)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        $media = $contract->addMedia(storage_path('tmp/uploads/' . $name))->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'contracts3');

        return response()->json([
            'contract' => new ContractResource($contract),
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
            'url' => $media->getFullUrl()
        ]);
    }

    /**
     * Store contract from API
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $direction = null;
        $api = true;
        $user = User::find(Auth::user()->id);
        $admins = User::where('type', 'admin')->get();

        $regex = "/^\d+(?:,\d+)*$/";
        $carriers = str_replace(' ', '', $request->carriers);

        if (!preg_match($regex, $carriers)) {
            return response()->json([
                'message' => 'The format for carriers is not correct',
            ], 400);
        }

        if ($request->direction) {
            $direction = $this->replaceDirection($request->direction);
        }

        //Saving contract
        $contract = Contract::create([
            'name' => $request->reference,
            'company_user_id' => Auth::user()->company_user_id,
            'direction_id' => $direction,
            'validity' =>  $request->valid_from,
            'expire' => $request->valid_until,
            'type' => $request->type,
        ]);

        //Saving contracts and carriers in ContractCarriers
        $contract->ContractCarrierSync($carriers, $api);

        //Uploading file to storage
        $contract->addMedia($request->file)->addCustomHeaders([
            'ACL' => 'public-read'
        ])->toMediaCollection('document', 'public');

        //Saving request FCL
        $Ncontract  = NewContractRequest::create([
            'namecontract' => $contract->name,
            'validation' => $contract->expire,
            'direction' => $contract->direction_id,
            'company_user_id' => $contract->company_user_id,
            'nameFile' => date("dmY_His") . '_' . $request->file->getClientOriginalName(),
            'user_id' => Auth::user()->id,
            'created' => date("Y-m-d H:i:s"),
            'username_load' => 'Not assigned',
            'contract_id' => $contract->id,
        ]);

        //Saving request and carriers in RequestCarriers
        $Ncontract->ContractRequestCarrierSync($carriers);

        //Dispatching jobs
        if (env('APP_VIEW') == 'operaciones') {
            ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request')->onQueue('operaciones');
        } else {
            ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request');
        }

        //Notifications
        $user->notify(new SlackNotification("There is a new request from " . $user->name . " - " . $user->companyUser->name));

        NotificationsJob::dispatch('Request-Fcl', [
            'user' => $user,
            'ncontract' => $Ncontract->toArray()
        ]);

        $Ncontract->NotifyNewRequest($admins);

        return response()->json([
            'message' => 'Contract created successfully!',
        ]);
    }

    /**
     * Check direction string and replace by id
     *
     * @param string $direction
     * @return void
     */
    public function replaceDirection($direction)
    {
        switch ($direction) {
            case 'import':
                $direction = 1;
                break;
            case 'export':
                $direction = 2;
                break;
            case 'both':
                $direction = 3;
                break;
            default:
                $direction = 3;
                break;
        }

        return $direction;
    }
}
