<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\Carrier;
use App\Company;
use App\Container;
use App\Contract;
use App\ContractLcl;
use App\Country;
use App\Currency;
use App\Direction;
use App\GroupContainer;
use App\Harbor;
use App\Http\Requests\UploadContractFile;
use App\Http\Resources\ContractResource;
use App\Jobs\NotificationsJob;
use App\Jobs\ProcessContractFile;
use App\LocalCharCarrier;
use App\LocalCharge;
use App\LocalCharPort;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\Notifications\SlackNotification;
use App\Rate;
use App\Surcharge;
use App\TypeDestiny;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use HelperAll;
use App\Jobs\ValidateTemplateJob;

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
    function list(Request $request)
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
            $company->name = $company->getFullNameAttribute();
            return $company->only(['id', 'name']);
        });


        // Export Data 

        $ori_harbors =   Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $des_harbors =   Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $ori_countries = Country::get()->map(function ($country) {
            $country['display_name'] = $country['name'];
            return $country->only(['id', 'display_name', 'name']);
        });

        $des_countries = Country::get()->map(function ($country) {
            $country['display_name'] = $country['name'];
            return $country->only(['id', 'display_name', 'name']);
        });


        //Roles
        $user = User::find(Auth::user()->id);
        $rol = $user->getRoleNames()->first();

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
            'rol',
            'users',
            'ori_harbors',
            'des_harbors',
            'ori_countries',
            'des_countries'
        );

        return response()->json(['data' => $data]);
    }

    /**
     * Display the specified resource collection.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function surcharge_data(Request $request, Contract $contract)
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
            'gp_container' => 'required',
            'carriers' => 'required',
        ]);

        $contract = Contract::create([
            'name' => $data['name'],
            'number' => null,
            'company_user_id' => $company_user_id,
            'user_id' => Auth::user()->id,
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => 'publish',
            'gp_container_id' => $data['gp_container'],
            'remarks' => '',
            'is_manual' => 1
        ]);

        $contract->createCustomCode();
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
            'carriers' => 'required',
        ]);

        $status = $this->updateStatus($contract, $data);

        $contract->update([
            'name' => $data['name'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $status,
            'gp_container_id' => $data['gp_container'],
        ]);

        $contract->ContractCarrierSync($data['carriers']);

        return new ContractResource($contract);
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
            'users' => 'sometimes',
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
            'remarks' => 'sometimes',
        ]);

        $contract->update(['remarks' => @$data['remarks']]);

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
        $status_erased = 1;
        if ($contract->status == 'incomplete' || $contract->status == 'Clarification needed') {

            $requestContract = NewContractRequest::where('contract_id', $contract->id);
            if (empty($requestContract) == 0) {

                $requestContract->update(['erased_contract' => $status_erased]);
            }
        }
        // $contract->delete();
        $contract->status_erased = $status_erased;
        $contract->name = $contract->name.'-'.$contract->code;
        $contract->code = null;
        $contract->contract_code = null;
        $contract->update();

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
        $new_contract->update([
            'user_id' => Auth::user()->id,
        ]);
        return new ContractResource($new_contract);
    }

    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        $status_erased = 1;
        DB::table('contracts')->whereIn('id', $request->input('ids'))->update(['status_erased' =>  $status_erased, 'contract_code' => null]);

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
    public function getFiles(Request $request, Contract $contract)
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
    public function storeMedia(Request $request, Contract $contract)
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
            'contract' => new ContractResource($contract),
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'url' => $media->getFullUrl(),
        ]);
    }

    /**
     * Store contract from API
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processUploadRequest(UploadContractFile $request)
    {
        try {
            $direction = null;
            $api = true;
            $user = User::findOrFail(Auth::user()->id);
            $admins = User::isAdmin()->get();
            $type = strtoupper($request->type);

            if ($request->code) {
                $query = Contract::where('code', $request->code);
                $query_lcl = ContractLcl::where('code', $request->code);
            } else {
                $query = Contract::where('code', $request->reference);
                $query_lcl = ContractLcl::where('code', $request->reference);
            }

            $contract = $query->first();
            $contract_lcl = $query_lcl->first();

            if ($contract != null || $contract_lcl != null) {
                return response()->json(['message' => 'There is already a contract with the code/reference entered'], 400);
            }

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

            $Ncontract = $this->uploadContract($request, $carriers, $api, $direction, $type);

            //Dispatching jobs
            if ($type == 'FCL') {
                if (env('APP_VIEW') == 'operaciones') {
                    ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request')->onQueue('operaciones');
                } else {
                    ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request');
                }
            } else {
                if (env('APP_VIEW') == 'operaciones') {
                    ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'lcl', 'request')->onQueue('operaciones');
                } else {
                    ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'lcl', 'request');
                }
            }

            //Notifications
            $user->notify(new SlackNotification("There is a new request from " . $user->name . " - " . $user->companyUser->name));

            NotificationsJob::dispatch('Request-' . $type, [
                'user' => $user,
                'ncontract' => $Ncontract->toArray(),
            ]);

            $Ncontract->NotifyNewRequest($admins);
            
            if (env('APP_VIEW') == 'operaciones') {
                ValidateTemplateJob::dispatch($Ncontract->id)->onQueue('operaciones');
            } else {
                ValidateTemplateJob::dispatch($Ncontract->id);
            }
            
            return response()->json([
                'message' => 'Contract created successfully!',
            ]);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong on our side',
            ], 500);
        }
    }

    /**
     * process contract and create request from API
     *
     * @param  mixed $request
     * @param  mixed $carriers
     * @param  mixed $api
     * @param  mixed $direction
     * @param  mixed $type
     * @return object
     */
    public function uploadContract($request, $carriers, $api, $direction, $type)
    {
        try {

            //Saving contract
            $contract = $this->storeContractApi($request, $direction, $type);

            //Saving contracts and carriers in ContractCarriers
            $contract->ContractCarrierSync($carriers, $api);

            //Creating custom code
            $contract->createCustomCode();

            $filename = date("dmY_His") . '_' . $request->file->getClientOriginalName();

            //Uploading file to storage
            $filename = quitar_caracteres($filename);
            $contract->StoreInMedia($request->file, $filename);

            //Saving request FCL
            $Ncontract = $this->storeContractRequest($contract, $filename, $type);

            //Saving request and carriers in RequestCarriers
            $Ncontract->ContractRequestCarrierSync($carriers, $api);

            return $Ncontract;
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something went wrong on our side',
            ], 500);
        }
    }

    /**
     * store contract from API in DB
     *
     * @param  mixed $request
     * @param  mixed $direction
     * @param  mixed $type
     * @return object
     */
    public function storeContractApi($request, $direction, $type)
    {
        $code = $request->code ?? null;

        switch ($type) {
            case 'FCL':
                $contract = Contract::create([
                    'name' => $request->reference,
                    'company_user_id' => Auth::user()->company_user_id,
                    'user_id' => Auth::user()->id,
                    'direction_id' => $direction,
                    'validity' => $request->valid_from,
                    'expire' => $request->valid_until,
                    'status' => 'incomplete',
                    'type' => $type,
                    'gp_container_id' => 1,
                    'code' => $code,
                    'is_api' => 1,
                ]);
                break;
            case 'LCL':
                $contract = ContractLcl::create([
                    'name' => $request->reference,
                    'company_user_id' => Auth::user()->company_user_id,
                    'direction_id' => $direction,
                    'validity' => $request->valid_from,
                    'expire' => $request->valid_until,
                    'status' => 'incomplete',
                    'type' => $type,
                    'code' => $code,
                    'is_api' => 1,
                ]);
                break;
        }

        return $contract;
    }

    /**
     * store request of contract in DB
     *
     * @param  mixed $contract
     * @param  mixed $filename
     * @param  mixed $type
     * @return object
     */
    public function storeContractRequest($contract, $filename, $type)
    {
        switch ($type) {
            case 'FCL':
                $request = NewContractRequest::create([
                    'namecontract' => $contract->name,
                    'code' => $contract->code,
                    'is_api' => $contract->is_api,
                    'validation' => $contract->validity . ' / ' . $contract->expire,
                    'direction_id' => $contract->direction_id,
                    'company_user_id' => $contract->company_user_id,
                    'namefile' => $filename,
                    'user_id' => Auth::user()->id,
                    'created' => date("Y-m-d H:i:s"),
                    'username_load' => 'Not assigned',
                    'data' => '{"containers": [{"id": 1, "code": "20DV", "name": "20 DV"}, {"id": 2, "code": "40DV", "name": "40 DV"}, {"id": 3, "code": "40HC", "name": "40 HC"}], "group_containers": {"id": 1, "name": "DRY"}, "contract":{"code":"' . $contract->code . '","is_api":' . $contract->is_api . '}}',
                    'contract_id' => $contract->id,
                ]);
                break;
            case 'LCL':
                $request = NewContractRequestLcl::create([
                    'namecontract' => $contract->name,
                    'code' => $contract->code,
                    'is_api' => $contract->is_api,
                    'validation' => $contract->validity . ' / ' . $contract->expire,
                    'direction' => $contract->direction_id,
                    'company_user_id' => $contract->company_user_id,
                    'namefile' => $filename,
                    'user_id' => Auth::user()->id,
                    'created' => date("Y-m-d H:i:s"),
                    'username_load' => 'Not assigned',
                    'contract_id' => $contract->id,
                ]);
                break;
        }

        return $request;
    }

    /**
     * Check direction string and replace by id
     *
     * @param string $direction
     * @return integer
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

    public function storeContractSearch(Request $request)
    {

        $req = $request->group_containerC;
        $contract = new Contract();
        $container = Container::get();

        $data = $request->validate([
            'referenceC' => 'required',
            'group_containerC' => 'required',
            'C20DV' => 'sometimes|required',
            'C40DV' => 'sometimes|required',
            'C40HC' => 'sometimes|required',
            'C40NOR' => 'sometimes|required',
            'C45HC' => 'sometimes|required',
            'amountC' => 'sometimes|required',
            'document' => 'required',
        ]);
        // dd(Auth::user()->id);
        $contract->company_user_id = Auth::user()->company_user_id;
        $contract->name = $request->referenceC;
        $validation = explode('/', $request->validityC);
        $contract->direction_id = $request->directionC;
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->status = 'publish';
        $contract->gp_container_id = $request->group_containerC;
        $contract->is_manual = 2;
        $contract->user_id = Auth::user()->id;
        $contract->save();

        $contract->ContractCarrierSyncSingle($request->carrierR);

        //Creating custom code
        $contract->createCustomCode();

        $rates = new Rate();
        $rates->origin_port = $request->origin_port;
        $rates->destiny_port = $request->destination_port;
        $arreglo = array();
        if ($req == 1) {

            $rates->twuenty = $request->C20DV;
            $rates->forty = $request->C40DV;
            $rates->fortyhc = $request->C40HC;
            $rates->fortynor = $request->C40NOR;
            $rates->fortyfive = $request->C45HC;
        } else {

            $rates->twuenty = 0;
            $rates->forty = 0;
            $rates->fortyhc = 0;
            $rates->fortynor = 0;
            $rates->fortyfive = 0;

            foreach ($container as $cod) {

                $cont = 'C' . $cod->code;
                if ($cod->gp_container_id == $req) {
                    $arreglo[$cont] = $request->{$cont};
                }
            }
            $rates->containers = json_encode($arreglo);
        }
        $rates->carrier_id = $request->carrierR;
        $rates->currency_id = $request->currencyR;
        $rates->contract()->associate($contract);
        $rates->save();

        // Surcharges

        $calculation_type = $request->input('calculation');
        $typeC = $request->input('type');
        $currencyC = $request->input('currency');
        $amountC = $request->input('amount');

        if (count((array)$calculation_type) > 0) {
            foreach ($calculation_type as $ct => $ctype) {

                if (!empty($request->input('amount'))) {
                    $localcharge = new LocalCharge();
                    $localcharge->surcharge_id = $typeC[$ct];
                    $localcharge->typedestiny_id = '3';
                    $localcharge->calculationtype_id = $ctype;
                    $localcharge->ammount = $amountC[$ct];
                    $localcharge->currency_id = $currencyC[$ct];
                    $localcharge->contract()->associate($contract);
                    $localcharge->save();

                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id = $request->carrierR; //$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();

                    $detailport = new LocalCharPort();
                    $detailport->port_orig = $request->origin_port; // $request->input('port_origlocal'.$contador.'.'.$orig);
                    $detailport->port_dest = $request->destination_port; //$request->input('port_destlocal'.$contador.'.'.$dest);
                    $detailport->localcharge()->associate($localcharge);
                    $detailport->save();
                }
            }
        }

        foreach ($request->input('document', []) as $file) {
            $contract->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('document', 'contracts3');
        }

        return response()->json([
            //'data' => $localcharge->toJson(),
            'data' => 'Success',
        ]);
    }

    public function getRequestStatus(Contract $contract)
    {

        if (!is_null($contract->contract_request)) {
            $request_status = $contract->contract_request->status;
            if ($request_status == "Pending") {
                $progress = 25;
            } else if ($request_status == "Processing") {
                $progress = 50;
            } else if ($request_status == "Imp Finished") {
                $progress = 75;
            } else if ($request_status == "Review") {
                $progress = 90;
            } else {
                $progress = 100;
            }
        } else {
            if ($contract->status == "incomplete") {
                $progress = 50;
            } else {
                $progress = 100;
            }
        }

        return response()->json([
            'progress' => $progress,
        ]);
    }
}
