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
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\Notifications\SlackNotification;
use App\Surcharge;
use App\TypeDestiny;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    function list(Request $request) {
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
            'account_id' => null,
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => 'publish',
            'gp_container_id' => $data['gp_container'],
            'remarks' => '',
            'is_manual' => 1,
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
            'carriers' => 'required',
        ]);

        $status = $this->updateStatus($data['expire']);

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

    public function updateStatus($data)
    {

        $date = date('Y-m-d');
        $expire = date('Y-m-d', strtotime($data));

        if ($expire <= $date) {
            $status = 'expired';
        } else {
            $status = 'publish';
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

        $file->move($path, $name);

        $media = $contract->addMedia(storage_path('tmp/uploads/' . $name))->addCustomHeaders([
            'ACL' => 'public-read',
        ])->toMediaCollection('document', 'contracts3');

        return response()->json([
            'contract' => new ContractResource($contract),
            'name' => $name,
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
        //Saving contract
        $contract = $this->storeContractApi($request, $direction, $type);

        //Saving contracts and carriers in ContractCarriers
        $contract->ContractCarrierSync($carriers, $api);

        $filename = date("dmY_His") . '_' . $request->file->getClientOriginalName();

        //Uploading file to storage
        $contract->StoreInMedia($request->file, $filename);

        //Saving request FCL
        $Ncontract = $this->storeContractRequest($contract, $filename, $type);

        //Saving request and carriers in RequestCarriers
        $Ncontract->ContractRequestCarrierSync($carriers, $api);

        return $Ncontract;
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

        if ($request->code) {
            $code = $request->code;
        } else {
            $code = $request->reference;
        }

        switch ($type) {
            case 'FCL':
                $contract = Contract::create([
                    'name' => $request->reference,
                    'company_user_id' => Auth::user()->company_user_id,
                    'direction_id' => $direction,
                    'validity' => $request->valid_from,
                    'expire' => $request->valid_until,
                    'status' => 'incomplete',
                    'type' => $type,
                    'gp_container_id' => 1,
                    'code' => $request->reference,
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
                    'code' => $request->reference,
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
                    'validation' => $contract->validity . ' / ' . $contract->expire,
                    'direction_id' => $contract->direction_id,
                    'company_user_id' => $contract->company_user_id,
                    'namefile' => $filename,
                    'user_id' => Auth::user()->id,
                    'created' => date("Y-m-d H:i:s"),
                    'username_load' => 'Not assigned',
                    'data' => '{"containers": [{"id": 1, "code": "20DV", "name": "20 DV"}, {"id": 2, "code": "40DV", "name": "40 DV"}, {"id": 3, "code": "40HC", "name": "40 HC"}, {"id": 4, "code": "45HC", "name": "45 HC"}, {"id": 5, "code": "40NOR", "name": "40 NOR"}], "group_containers": {"id": 1, "name": "DRY"}}',
                    'contract_id' => $contract->id,
                ]);
                break;
            case 'LCL':
                $request = NewContractRequestLcl::create([
                    'namecontract' => $contract->name,
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

        $contract->company_user_id = Auth::user()->company_user_id;
        $contract->name = $request->referenceC;
        $validation = explode('/', $request->validityC);
        $contract->direction_id = $request->directionC;
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->status = 'publish';
        $contract->gp_container_id = $request->group_containerC;
        $contract->save();
        return response()->json([
            'data' => 'Contract created successfully!',
        ]);
/*
$details = $request->input('currency_id');
$detailscharges = $request->input('localcurrency_id');
$companies = $request->input('companies');
$users = $request->input('users');

// All IDS
$carrierAllid = $this->allCarrierid();
$countryAllid = $this->allCountryid();
$portAllid = $this->allHarborid();

// For Carrier in ContractCarrier Model
foreach($request->carrierAr as $carrierFA){
ContractCarrier::create([
'carrier_id'    => $carrierFA,
'contract_id'   => $contract->id
]);
}
// For Each de los rates
$contador = 1;
$contadorRate = 1;

// For each de los rates
foreach($details as $key => $value)
{

$rateOrig  = $request->input('origin_id'.$contadorRate);
$rateDest  = $request->input('destiny_id'.$contadorRate);

foreach($rateOrig as $Rorig => $Origvalue)
{
foreach($rateDest as $Rdest => $Destvalue)
{
$sch = null;
if($request->input('scheduleT.'.$key) != 'null'){
$sch = $request->input('scheduleT.'.$key);
}
$rates = new Rate();
$rates->origin_port         = $request->input('origin_id'.$contadorRate.'.'.$Rorig);
$rates->destiny_port        = $request->input('destiny_id'.$contadorRate.'.'.$Rdest);
$rates->carrier_id          = $request->input('carrier_id.'.$key);
$rates->twuenty             = $request->input('twuenty.'.$key);
$rates->forty               = $request->input('forty.'.$key);
$rates->fortyhc             = $request->input('fortyhc.'.$key);
$rates->fortynor            = $request->input('fortynor.'.$key);
$rates->fortyfive           = $request->input('fortyfive.'.$key);
$rates->currency_id         = $request->input('currency_id.'.$key);
$rates->schedule_type_id    = $sch;
$rates->transit_time        = $request->input('transitTi.'.$key);
$rates->via                 = $request->input('via.'.$key);
$rates->contract()->associate($contract);
$rates->save();
}
}
$contadorRate++;
}
// For Each de los localcharge

foreach($detailscharges as $key2 => $value)
{
$calculation_type = $request->input('calculationtype'.$contador);
if(!empty($calculation_type)){

foreach($calculation_type as $ct => $ctype)
{

if(!empty($request->input('ammount.'.$key2))) {
$localcharge = new LocalCharge();
$localcharge->surcharge_id = $request->input('type.'.$key2);
$localcharge->typedestiny_id = $request->input('changetype.'.$key2);
$localcharge->calculationtype_id = $ctype;//$request->input('calculationtype.'.$key2);
$localcharge->ammount = $request->input('ammount.'.$key2);
$localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
$localcharge->contract()->associate($contract);
$localcharge->save();

$detailcarrier = $request->input('localcarrier_id'.$contador);
$detailcarrier = $this->arrayAll($detailcarrier,$carrierAllid);     // Consultar el all en carrier

foreach($detailcarrier as $c => $valueCarrier)
{
$detailcarrier = new LocalCharCarrier();
$detailcarrier->carrier_id =  $valueCarrier;//$request->input('localcarrier_id'.$contador.'.'.$c);
$detailcarrier->localcharge()->associate($localcharge);
$detailcarrier->save();
}

$typeroute =  $request->input('typeroute'.$contador);
if($typeroute == 'port'){
$detailportOrig = $request->input('port_origlocal'.$contador);
$detailportDest = $request->input('port_destlocal'.$contador);

$detailportOrig = $this->arrayAll($detailportOrig,$portAllid);     // Consultar el all en origen
$detailportDest = $this->arrayAll($detailportDest,$portAllid);      // Consultar el all en Destino

foreach($detailportOrig as $orig => $valueOrig)
{
foreach($detailportDest as $dest => $valueDest)
{
$detailport = new LocalCharPort();
$detailport->port_orig =$valueOrig; // $request->input('port_origlocal'.$contador.'.'.$orig);
$detailport->port_dest = $valueDest;//$request->input('port_destlocal'.$contador.'.'.$dest);
$detailport->localcharge()->associate($localcharge);
$detailport->save();
}

}
}elseif($typeroute == 'country'){

$detailcountryOrig = $request->input('country_orig'.$contador);
$detailcountryDest = $request->input('country_dest'.$contador);

// ALL
$detailcountryOrig = $this->arrayAll($detailcountryOrig,$countryAllid);     // Consultar el all en origen
$detailcountryDest = $this->arrayAll($detailcountryDest,$countryAllid);      // Consultar el all en Destino

foreach($detailcountryOrig as $origC => $originCounty)
{
foreach($detailcountryDest as $destC => $destinyCountry)
{
$detailcountry = new LocalCharCountry();
$detailcountry->country_orig = $originCounty;//$request->input('country_orig'.$contador.'.'.$origC);
$detailcountry->country_dest = $destinyCountry; //;$request->input('country_dest'.$contador.'.'.$destC);
$detailcountry->localcharge()->associate($localcharge);
$detailcountry->save();
}
}
}

}
}
}
$contador++;
}

if(!empty($companies)){
foreach($companies as $key3 => $value)
{
$contract_company_restriction = new ContractCompanyRestriction();
$contract_company_restriction->company_id=$value;
$contract_company_restriction->contract_id=$contract->id;
$contract_company_restriction->save();
}
}

if(!empty($users)){
foreach($users as $key4 => $value)
{
$contract_client_restriction = new ContractUserRestriction();
$contract_client_restriction->user_id=$value;
$contract_client_restriction->contract_id=$contract->id;
$contract_client_restriction->save();
}
}

foreach ($request->input('document', []) as $file) {
$contract->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('document','contracts3');
}

//$request->session()->flash('message.nivel', 'success');
//$request->session()->flash('message.title', 'Well done!');
//$request->session()->flash('message.content', 'You successfully add this contract.');
return redirect()->route('contracts.edit', [setearRouteKey($contract->id)]);
//return redirect()->action('ContractsController@index');
 */
    }

}
