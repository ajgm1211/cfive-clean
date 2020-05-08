<?php

namespace App\Http\Controllers;

use Excel;
use App\User;
use App\Rate;
use App\Harbor;
use App\Country;
use App\Contact;
use App\Carrier;
use App\FileTmp;
use App\Company;
use App\Contract;
use App\FailRate;
use App\Currency;
use PrvValidation;

use App\Direction;
use App\Surcharge;
use App\ViewRates;
use \Carbon\Carbon;
use App\CompanyUser;
use App\TypeDestiny;
use App\LocalCharge;
use App\ScheduleType;
use App\FailSurCharge;
use App\LocalCharPort;
use App\ContractAddons;
use App\Jobs\GeneralJob;
use App\ContractCarrier;
use App\CalculationType;
use App\LocalCharCountry;
use App\LocalCharCarrier;
use App\ViewLocalCharges;
use App\ViewContractRates;
use App\NewContractRequest;
use Illuminate\Http\Request;
use App\ContractUserRestriction;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\ContractCompanyRestriction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaStream;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportationRatesSurchargerJob;
use App\Http\Requests\UploadFileRateRequest;
use Illuminate\Support\Collection as Collection;

class ContractsController extends Controller
{

    public function index(Request $request)
    {

        /*
        if(\Auth::user()->type=='admin'){
            $arreglo    = Contract::with('rates','carriers','direction')->get();
            $contractG  = Contract::all();

        }else{
            $arreglo    = Contract::where('company_user_id','=',Auth::user()->company_user_id)
                ->with('rates','carriers','direction')->get();
            $contractG  = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();
        }*/

        $originsR = DB::table('views_contract_rates')
            ->select('port_orig')
            ->groupBy('port_orig')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $destinationsR = DB::table('views_contract_rates')
            ->select('port_dest')
            ->groupBy('port_dest')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $carriersR = DB::table('views_contract_rates')
            ->select('carrier')
            ->groupBy('carrier')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $statussR = DB::table('views_contract_rates')
            ->select('status')
            ->groupBy('status')
            ->where('company_user_id', Auth::user()->company_user_id)
            ->get();

        $carrierAr = [ 'null' => 'Select option'];
        $originsAr = [ 'null' => 'Select option'];
        $destinationAr = [ 'null' => 'Select option'];
        $statusAr  = [ 'null' => 'Select option'];

        foreach($carriersR as $carrierR){
            $carrierAr[$carrierR->carrier] = $carrierR->carrier;
        }

        foreach($originsR as $originR){
            $originsAr[$originR->port_orig] = $originR->port_orig;
        }

        foreach($destinationsR as $destinationR){
            $destinationAr[$destinationR->port_dest] = $destinationR->port_dest;
        }

        foreach($statussR as $statusR){
            $statusAr[$statusR->status] = $statusR->status;
        }
        $values = [
            'carrier'       => $carrierAr,
            'origin'        => $originsAr,
            'destination'   => $destinationAr,
            'status'        => $statusAr
        ];
        return view('contracts/index', compact('arreglo','contractG','values'));
    }

    public function add()
    {
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();

        $harbor     = $objharbor->all()->pluck('display_name','id');
        $country    = $objcountry->all()->pluck('name','id');
        $carrier    = $objcarrier->all()->pluck('name','id');
        $scheduleT   = ['null'=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $scheduleT[$d['id']]=$d->name;
        }
        $direction  = [null=>'Please Select'];
        $direction2 = Direction::all();
        foreach($direction2 as $d){
            $direction[$d['id']]=$d->name;
        }
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
        $contacts = Contact::whereHas('company', function ($query) {
            $query->where('company_user_id', '=', \Auth::user()->company_user_id);
        })->pluck('first_name','id');
        if(Auth::user()->type == 'company' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }

        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);

        return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny','companies','contacts','users','currency_cfg','direction','scheduleT'));


    }

    public function create()
    {
        //
    }

    function arrayAll($array,$id){


        if (in_array($id, $array)) {
            $id = array($id);
            return $id;
        }else{
            return $array;
        }
    }
    function allHarborid(){
        $id = Harbor::where('code','ALL')->first();
        return $id->id;
    }
    function allCountryid(){
        $id = Country::where('code','ALL')->first();
        return $id->id;
    }

    function allCarrierid(){
        $id = Carrier::where('name','ALL')->first();
        return $id->id;
    }
    public function deleteMedia($id,$contract_id)
    {

        $contract = Contract::find($contract_id);
        $media = Media::find($id);
        $contract->deleteMedia($media->id);



        //    $media->delete();
        return 'true';
    }
    public function getMedia(Media $mediaItem)
    {
        return $mediaItem;
    }
    public function getMediaAll($id){

        $contract = Contract::find($id);
        $downloads = $contract->getMedia('document');

        // Download the files associated with the media in a streamed way.
        // No prob if your files are very large.
        return MediaStream::create('my-contract.zip')->addMedia($downloads);
    }

    public function getMediaSimple($id){


        $mediaItem = Media::find($id);
        return $this->getMedia($mediaItem);



    }

    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function store(Request $request)
    {

        $contract = new Contract($request->all());
        $contract->company_user_id =Auth::user()->company_user_id;
        $validation = explode('/',$request->validation_expire);
        $contract->direction_id = $request->direction;
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->save();

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

    }

    public function showContractRequest($id){
        $contract   = Contract::with('companyuser','direction','carriers')->find($id);
        $directions = Direction::pluck('name','id');
        $carriers   = Carrier::pluck('name','id');
        //dd($contract);
        return view('RequestV2.Fcl.Body-Modals.editContract',compact('contract','directions','carriers'));
    }
    public function updateContractRequest(Request $request,$id){
        //dd($request->all()); 
        $contract                   = Contract::find($id);
        $contract->name             = $request->name;
        $contract->company_user_id  = $request->company_user_id;
        $validation                 = explode('/',$request->validation_expire);
        $contract->direction_id     = $request->direction_id;
        $contract->validity         = $validation[0];
        $contract->expire           = $validation[1];
        $contract->update();

        foreach($request->carriers_id as $carrierFA){
            ContractCarrier::create([
                'carrier_id'    => $carrierFA,
                'contract_id'   => $contract->id
            ]);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your contract was updated');
        return redirect()->route('RequestFcl.index');
    }

    public function show($id)
    {
        //
    }

    // FUNCIONES PARA EL DATATABLE
    public function data($id){

        /*   $localchar = new  ViewLocalCharges();
        $data = $localchar->select('id','surcharge','port_orig','port_dest','country_orig','country_dest','changetype','carrier','calculation_type','ammount','currency')->where('contract_id',$id);*/
        $data1 = \DB::select(\DB::raw('call proc_localchar('.$id.')'));
        $data = new Collection;
        for ($i = 0; $i < count($data1); $i++) {
            $data->push([
                'id' => $data1[$i]->id,
                'surcharge' =>  $data1[$i]->surcharge,
                'port_orig' =>   $data1[$i]->port_orig,
                'port_dest' =>   $data1[$i]->port_dest,
                'country_orig' =>  $data1[$i]->country_orig,
                'country_dest' =>   $data1[$i]->country_dest,
                'changetype' =>  $data1[$i]->changetype,
                'carrier' =>   $data1[$i]->carrier,
                'calculation_type' => $data1[$i]->calculation_type,
                'ammount' =>   $data1[$i]->ammount,
                'currency' =>   $data1[$i]->currency,

            ]);
        }
        return \DataTables::of($data)
            ->addColumn('origin', function ($data) {
                if($data['country_orig'] != null){
                    return $data['country_orig'];
                }else{
                    return $data['port_orig'];
                }

            })
            ->addColumn('destiny', function ($data) {
                if($data['country_dest'] != null){
                    return $data['country_dest'];
                }else{
                    return $data['port_dest'];
                }
            })
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Edit '  onclick='AbrirModal(\"editLocalCharge\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
            <a  data-local-id='$data[id]'    class='m_sweetalert_demo_8  m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='delete' >
          <i id='rm_l' class='la la-times-circle'></i></a>
          <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Duplicate '  onclick='AbrirModal(\"duplicateLocalCharge\",$data[id])'>
          <i class='la la-plus'></i>
          </a>
        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);
    }// local charges en edit

    public function dataRates($id){

        $rate = new  ViewRates();
        $data = $rate->select('id','port_orig','port_dest','carrier','twuenty','forty','fortyhc','fortynor','fortyfive','currency','schedule_type','transit_time','via')->where('contract_id',$id);

        return \DataTables::of($data)
            ->addColumn('schedule_type', function ($data) {
                if(empty($data['schedule_type']) != true){
                    return $data['schedule_type'];
                } else {
                    return '-------------';
                }
            })
            ->addColumn('transit_time', function ($data) {
                if(empty($data['transit_time']) != true){
                    return $data['transit_time'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('via', function ($data) {
                if(empty($data['via']) != true){
                    return $data['via'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test' title='Edit'  onclick='AbrirModal(\"editRate\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
             <a id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>
                    <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test' title='Duplicate'  onclick='AbrirModal(\"duplicateRate\",$data[id])'>
          <i class='la la-plus'></i>
          </a>

        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }

    public function contractRates(Request $request){
        $contractRate = new  ViewContractRates();
        $data = $contractRate->select('id','contract_id','name','number','validy','expire','status','port_orig','port_dest','carrier','twuenty','forty','fortyhc','fortynor','fortyfive','currency','schedule_type','transit_time','via')->where('company_user_id', Auth::user()->company_user_id)->status($request->status)->carrier($request->carrier)->destPort($request->destination)->origPort($request->origin);

        return \DataTables::of($data)

            ->addColumn('schedule_type', function ($data) {
                if(empty($data['schedule_type']) != true){
                    return $data['schedule_type'];
                } else {
                    return '-------------';
                }
            })
            ->addColumn('transit_time', function ($data) {
                if(empty($data['transit_time']) != true){
                    return $data['transit_time'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('via', function ($data) {
                if(empty($data['via']) != true){
                    return $data['via'];
                } else {
                    return '-----';
                }
            })
            ->addColumn('validity', function ($data) {
                return $data['validy'] ." / ".$data['expire'];
            })
            ->addColumn('options', function ($data) {
                return "<a href='contracts/".setearRouteKey($data['contract_id'])."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>

                    <a href='#' id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>

        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }

    public function contractTable(){

        $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->with('carriers.carrier','direction')->get();
        //dd($contractG);

        return \DataTables::collection($contractG)

            ->addColumn('direction', function (Contract $contractG) {
                if(count($contractG->direction) != 0){
                    return $contractG->direction->name;
                } else {
                    return '-----------------';
                }
            }) 
            ->addColumn('carrier', function (Contract $contractG) {
                if(count($contractG->carriers->pluck('carrier')->pluck('name')) != 0){
                    return str_replace(['[',']','"'],' ',$contractG->carriers->pluck('carrier')->pluck('name'));
                } else {
                    return '-----------------';
                }
            })
            ->addColumn('options', function (Contract $contractG) {

                return "<a href='contracts/".setearRouteKey($contractG->id)."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>
                    <a href='#' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContract\",".($contractG->id).")'  title='Duplicate '>
                      <i class='la la-copy'></i>
                    </a>
                    <a  id='delete-contract' data-contract-id='$contractG->id' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Delete'>
                      <i class='la la-eraser'></i>
                    </a>";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }
    public function edit(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $contracts = Contract::where('id',$id)->with('direction','carriers.carrier')->first();
        //dd($contracts->carriers->pluck('carrier'));

        $objtypedestiny = new TypeDestiny();
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();

        $harbor = $objharbor->all()->pluck('display_name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $direction = Direction::pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $company_restriction = ContractCompanyRestriction::where('contract_id',$contracts->id)->get();
        $user_restriction = ContractUserRestriction::where('contract_id',$contracts->id)->get();
        if(!empty($company_restriction)){

            $company = Company::wherein('id',$company_restriction->pluck('company_id'))->get();
            $company = $company->pluck('id');

        }

        if(!empty($user_restriction)){
            $user = User::wherein('id',$user_restriction->pluck('user_id'))->get();
            $user = $user->pluck('id');
        }
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
        if(Auth::user()->type == 'company' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        //dd($contracts);
        if (!$request->session()->exists('activeS')) {
            $request->session()->flash('activeR', 'active');
        }

        //Items
        $mediaItems = $contracts->getMedia('document');
        $totalItems = count($mediaItems);
        if($totalItems=='0'){

            $message =   'Do you want add files to this contract?';

        }else{
            $message =   'Do you want edit o remove this files?';

        }



        return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','company','companies','users','user','id','direction','mediaItems','totalItems','message'));
    }

    public function update(Request $request, $id)
    {
        $requestForm            = $request->all();
        $contract               = Contract::find($id);
        $validation             = explode('/',$request->validation_expire);
        $contract->direction_id = $request->direction;
        $contract->validity     = $validation[0];
        $contract->expire       = $validation[1];
        $contract->update($requestForm);

        $companies = $request->input('companies');
        $users = $request->input('users');

        /*
        $details = $request->input('origin_id');
        $detailscharges =  $request->input('localcurrency_id');//  $request->input('ammount');
        $companies = $request->input('companies');
        $users = $request->input('users');
        $contador = 1;
        // for each rates
        foreach($details as $key => $value)
        {
          if(is_numeric($request->input('twuenty.'.$key))) {

            $rates = new Rate();
            $rates->origin_port = $request->input('origin_id.'.$key);
            $rates->destiny_port = $request->input('destiny_id.'.$key);
            $rates->carrier_id = $request->input('carrier_id.'.$key);
            $rates->twuenty = $request->input('twuenty.'.$key);
            $rates->forty = $request->input('forty.'.$key);
            $rates->fortyhc = $request->input('fortyhc.'.$key);
            $rates->currency_id = $request->input('currency_id.'.$key);
            $rates->contract()->associate($contract);
            $rates->save();

          }
        }

        // For Each de los localcharge

        foreach($detailscharges as $key2 => $value)
        {
          if(!empty($request->input('ammount.'.$key2))) {
            $localcharge = new LocalCharge();
            $localcharge->surcharge_id = $request->input('type.'.$key2);
            $localcharge->typedestiny_id  = $request->input('changetype.'.$key2);
            $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
            $localcharge->ammount = $request->input('ammount.'.$key2);
            $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
            $localcharge->contract()->associate($contract);
            $localcharge->save();

            $detailportOrig = $request->input('port_origlocal'.$contador);
            $detailportDest = $request->input('port_destlocal'.$contador);
            // ALL
            $detailportOrig = $this->arrayAll($detailportOrig,$portAllid);     // Consultar el all en origen
            $detailportDest = $this->arrayAll($detailportDest,$portAllid);      // Consultar el all en Destino


            $detailcarrier = $request->input('localcarrier_id'.$contador);
            $companies = $request->input('companies');
            foreach($detailcarrier as $c => $value)
            {
              $detailcarrier = new LocalCharCarrier();
              $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
              $detailcarrier->localcharge()->associate($localcharge);
              $detailcarrier->save();
            }
            foreach($detailportOrig as $orig => $valueOrig)
            {
              foreach($detailportDest as $dest => $valueDest)
              {

                $detailport = new LocalCharPort();
                $detailport->port_orig = $valueOrig; // $request->input('port_origlocal'.$contador.'.'.$orig);
                $detailport->port_dest = $valueDest// $request->input('port_destlocal'.$contador.'.'.$dest);
                  $detailport->localcharge()->associate($localcharge);
                $detailport->save();
              }

            }
            $contador++;
          }
        }*/

        ContractCarrier::where('contract_id',$id)->delete();
        foreach($request->carrierAr as $carrierFA){
            ContractCarrier::create([
                'carrier_id'    => $carrierFA,
                'contract_id'   => $id
            ]);
        }

        ContractCompanyRestriction::where('contract_id',$contract->id)->delete();

        if(!empty($companies)){
            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contract_id=$contract->id;
                $contract_company_restriction->save();
            }
        }




        ContractUserRestriction::where('contract_id',$contract->id)->delete();

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




        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'The contract was successfully updated');
        return redirect()->back()->with('editContract','true');

        //return redirect()->action('ContractsController@index');

    }

    public function addRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);
        $scheduleT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $scheduleT[$d['id']]=$d->name;
        } 

        return view('contracts.addRates', compact('harbor','carrier','currency','id','currency_cfg','scheduleT'));
    }
    public function storeRates(Request $request,$id){

        $rateOrig = $request->input('origin_port');
        $rateDest = $request->input('destiny_port');

        foreach($rateOrig as $Rorig => $Origvalue)
        {
            foreach($rateDest as $Rdest => $Destvalue)
            {
                $rates = new Rate();
                $rates->origin_port     = $Origvalue;
                $rates->destiny_port    = $Destvalue;
                $rates->carrier_id      = $request->input('carrier_id');
                $rates->twuenty         = $request->input('twuenty');
                $rates->forty           = $request->input('forty');
                $rates->fortyhc         = $request->input('fortyhc');
                $rates->fortynor        = $request->input('fortynor');
                $rates->fortyfive       = $request->input('fortyfive');
                $rates->currency_id     = $request->input('currency_id');
                $rates->schedule_type_id = $request->input('schedule_type_id');
                $rates->transit_time    = $request->input('transit_time');
                $rates->via             = $request->input('via');
                $rates->contract_id     = $id;
                $rates->save();
            }
        }
        return redirect()->back()->with('ratesSave','true');
    }
    public function editRates($id){
        $objcarrier     = new Carrier();
        $objharbor      = new Harbor();
        $objcurrency    = new Currency();
        $harbor         = $objharbor->all()->pluck('display_name','id');
        $carrier        = $objcarrier->all()->pluck('name','id');
        $currency       = $objcurrency->all()->pluck('alphacode','id');
        $schedulesT   = [null=>'Please Select'];
        $scheduleTo  = ScheduleType::all();
        foreach($scheduleTo as $d){
            $schedulesT[$d['id']]=$d->name;
        }
        $rates          = Rate::find($id);
        $rates->load('scheduletype');
        //dd($rates);
        return view('contracts.editRates', compact('rates','harbor','carrier','currency','schedulesT'));
    }
    public function updateRates(Request $request, $id){
        $requestForm = $request->all();
        $rate = Rate::find($id);
        $rate->update($requestForm);
        return redirect()->back()->with('editRate','true');
    }

    public function duplicateRates($id){
        $objcarrier     = new Carrier();
        $objharbor      = new Harbor();
        $objcurrency    = new Currency();
        $harbor         = $objharbor->all()->pluck('display_name','id');
        $carrier        = $objcarrier->all()->pluck('name','id');
        $currency       = $objcurrency->all()->pluck('alphacode','id');
        $schedulesT     = ScheduleType::pluck('name','id');
        $rates          = Rate::find($id);
        return view('contracts.duplicateRates', compact('rates','harbor','carrier','currency','schedulesT'));
    }

    public function addLocalChar($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();
        $countries = Country::pluck('name','id');
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $company_user=CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);

        return view('contracts.addLocalCharge', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','id','countries','currency_cfg'));

    }
    public function storeLocalChar(Request $request,$id){

        $calculation_type  = $request->input('calculationtype_id');
        // All IDS
        $carrierAllid = $this->allCarrierid();
        $countryAllid = $this->allCountryid();
        $portAllid = $this->allHarborid();
        foreach($calculation_type as $ct => $ctype)
        {
            $localcharge = new LocalCharge();
            $request->request->add(['contract_id' => $id,'calculationtype_id'=>$ctype]);
            $localcharge =  $localcharge->create($request->all());
            $detailcarrier = $request->input('carrier_id');
            $detailcarrier = $this->arrayAll($detailcarrier,$carrierAllid);     // Consultar el all en carrier
            foreach($detailcarrier as $c => $value)
            {
                $detailcarrier = new LocalCharCarrier();
                $detailcarrier->carrier_id =$value;
                $detailcarrier->localcharge()->associate($localcharge);
                $detailcarrier->save();
            }
            $typeroute =  $request->input('typeroute');
            if($typeroute == 'port'){
                $detailportOrig = $request->input('port_origlocal');
                $detailportDest = $request->input('port_destlocal');
                $detailportOrig = $this->arrayAll($detailportOrig,$portAllid);     // Consultar el all en origen
                $detailportDest = $this->arrayAll($detailportDest,$portAllid);      // Consultar el all en Destino

                foreach($detailportOrig as $orig => $valueOrig){
                    foreach($detailportDest as $dest => $valueDest){
                        $detailport = new LocalCharPort();
                        $detailport->port_orig =$valueOrig;
                        $detailport->port_dest =$valueDest;
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }
                }
            }elseif($typeroute == 'country'){

                $detailcountryOrig = $request->input('country_orig');
                $detailcountryDest = $request->input('country_dest');
                // ALL
                $detailcountryOrig = $this->arrayAll($detailcountryOrig,$countryAllid);     // Consultar el all en origen
                $detailcountryDest = $this->arrayAll($detailcountryDest,$countryAllid);      // Consultar el all en Destino

                foreach($detailcountryOrig as $orig => $valueOrigC){
                    foreach($detailcountryDest as $dest => $valueDestC){
                        $detailcountry = new LocalCharCountry();
                        $detailcountry->country_orig =$valueOrigC;
                        $detailcountry->country_dest = $valueDestC;
                        $detailcountry->localcharge()->associate($localcharge);
                        $detailcountry->save();

                    }
                }
            }
        }
        return redirect()->back()->with('localcharSave','true')->with('activeS','active');
    }
    public function editLocalChar($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objtypedestiny = new TypeDestiny();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $countries = Country::pluck('name','id');

        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $localcharges = LocalCharge::find($id);
        return view('contracts.editLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }
    public function updateLocalChar(Request $request, $id)
    {
        $localC = LocalCharge::find($id);
        // All IDS
        $carrierAllid = $this->allCarrierid();
        $countryAllid = $this->allCountryid();
        $portAllid = $this->allHarborid();

        $localC->surcharge_id = $request->input('surcharge_id');
        $localC->typedestiny_id  = $request->input('changetype');
        $localC->calculationtype_id = $request->input('calculationtype_id');
        $localC->ammount = $request->input('ammount');
        $localC->currency_id = $request->input('currency_id');
        $localC->update();


        $carrier = $request->input('carrier_id');
        $carrier = $this->arrayAll($carrier,$carrierAllid);     // Consultar el all en carrier

        $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPort::where("localcharge_id",$id);
        $deletePort->delete();
        $deleteCountry = LocalCharCountry::where("localcharge_id",$id);
        $deleteCountry->delete();
        $typerate =  $request->input('typeroute');
        if($typerate == 'port'){
            $detailportOrig = $request->input('port_origlocal');
            $detailportDest = $request->input('port_destlocal');
            $detailportOrig = $this->arrayAll($detailportOrig,$portAllid);     // Consultar el all en origen
            $detailportDest = $this->arrayAll($detailportDest,$portAllid);      // Consultar el all en Destino
            foreach($detailportOrig as $orig => $valueOrig)
            {
                foreach($detailportDest as $dest => $valueDest)
                {
                    $detailport = new LocalCharPort();
                    $detailport->port_orig = $valueOrig;
                    $detailport->port_dest = $valueDest;
                    $detailport->localcharge_id = $id;
                    $detailport->save();
                }
            }
        }elseif($typerate == 'country'){
            $detailCountrytOrig =$request->input('country_orig');
            $detailCountryDest = $request->input('country_dest');
            // ALL
            $detailCountrytOrig = $this->arrayAll($detailCountrytOrig,$countryAllid);     // Consultar el all en origen
            $detailCountryDest = $this->arrayAll($detailCountryDest,$countryAllid);      // Consultar el all en Destino
            foreach($detailCountrytOrig as $orig => $valueOrigC)
            {
                foreach($detailCountryDest as $dest => $valueDestC)
                {
                    $detailcountry = new LocalCharCountry();
                    $detailcountry->country_orig = $valueOrigC;
                    $detailcountry->country_dest =  $valueDestC;
                    $detailcountry->localcharge_id = $id;
                    $detailcountry->save();
                }
            }
        }

        foreach($carrier as $key)
        {
            $detailcarrier = new LocalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->localcharge_id = $id;
            $detailcarrier->save();
        }
        return redirect()->back()->with('localchar','true')->with('activeS','active');
    }

    public function duplicateLocalChar($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objtypedestiny = new TypeDestiny();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $countries = Country::pluck('name','id');

        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $localcharges = LocalCharge::find($id);
        return view('contracts.duplicateLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge','countries'));
    }
    public function destroy($id)
    {
        $rate = Rate::find($id);
        $rate->delete();
        return $rate;
    }

    public function deleteContract($id){

        $contract = Contract::find($id);
        $data = PrvValidation::ContractWithJob($id);
        if(isset($contract->rates)){
            if(isset($contract->localcharges)){
                return response()->json(['message' => count($contract->rates),'local' => count($contract->localcharges),'jobAssociate' => $data['bool'] ]);
            }else{
                return response()->json(['message' => count($contract->rates),'local' => 0,'jobAssociate' => $data['bool']]);
            }
        }
        return response()->json(['message' => 'SN','local' => 0,'jobAssociate' => $data['bool']]);
    }
    public function destroyContract($id){

        try {

            $FileTmp = FileTmp::where('contract_id',$id)->first();
            if(count($FileTmp) > 0){
                Storage::Delete($FileTmp->name_file);
                $FileTmp->delete();
            }

            $contract = Contract::find($id);
            $contract->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }


    }

    public function destroyLocalCharges($id)
    {
        $local = LocalCharge::find($id);
        $local->forceDelete();
    }

    public function destroyRates(Request $request,$id)
    {
        $rate = Rate::find($id);
        $rate->forceDelete();
        return $rate;
    }

    public function destroymsg($id)
    {
        return view('contracts/message' ,['rate_id' => $id]);
    }

    public function failRatesSurchrgesForNewContracts($id){

        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();
        $objsurcharge       = new Surcharge();

        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->all()->pluck('name','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');
        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        //------------------------------- Rates ---------------------------------------------------------------

        $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();

        $rates = Rate::with('carrier','contract','port_origin','port_destiny')->where('contract_id','=',$id)->get();
        $failratesFor = FailRate::where('contract_id','=',$id)->get();

        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;
        $failrates = collect([]);

        foreach( $failratesFor as $failrate){
            $carrAIn;
            $pruebacurre = "";
            $classdorigin='color:green';
            $classddestination='color:green';
            $classcarrier='color:green';
            $classcurrency='color:green';
            $classtwuenty ='color:green';
            $classforty ='color:green';
            $classfortyhc ='color:green';
            $originA =  explode("_",$failrate['origin_port']);
            //dd($originA);
            $destinationA = explode("_",$failrate['destiny_port']);
            $carrierA = explode("_",$failrate['carrier_id']);
            $currencyA = explode("_",$failrate['currency_id']);
            $twuentyA = explode("_",$failrate['twuenty']);
            $fortyA = explode("_",$failrate['forty']);
            $fortyhcA = explode("_",$failrate['fortyhc']);
            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
            $originAIn = $originOb['id'];
            $originC   = count($originA);
            if($originC <= 1){
                $originA = $originOb['name'];
            } else{
                $originA = $originA[0].' (error)';
                $classdorigin='color:red';
            }
            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
            $destinationAIn = $destinationOb['id'];
            $destinationC   = count($destinationA);
            if($destinationC <= 1){
                $destinationA = $destinationOb['name'];
            } else{
                $destinationA = $destinationA[0].' (error)';
                $classddestination='color:red';
            }
            $twuentyC   = count($twuentyA);
            if($twuentyC <= 1){
                $twuentyA = $twuentyA[0];
            } else{
                $twuentyA = $twuentyA[0].' (error)';
                $classtwuenty='color:red';
            }
            $fortyC   = count($fortyA);
            if($fortyC <= 1){
                $fortyA = $fortyA[0];
            } else{
                $fortyA = $fortyA[0].' (error)';
                $classforty='color:red';
            }
            $fortyhcC   = count($fortyhcA);
            if($fortyhcC <= 1){
                $fortyhcA = $fortyhcA[0];
            } else{
                $fortyhcA = $fortyhcA[0].' (error)';
                $classfortyhc='color:red';
            }
            $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
            $carrAIn = $carrierOb['id'];
            $carrierC = count($carrierA);
            if($carrierC <= 1){
                //dd($carrierAIn);
                $carrierA = $carrierA[0];
            }
            else{
                $carrierA = $carrierA[0].' (error)';
                $classcarrier='color:red';
            }
            $currencyC = count($currencyA);
            if($currencyC <= 1){
                $currenc = Currency::where('alphacode','=',$currencyA[0])->first();
                $pruebacurre = $currenc['id'];
                $currencyA = $currencyA[0];
            }
            else{
                $currencyA = $currencyA[0].' (error)';
                $classcurrency='color:red';
            }
            $colec = ['rate_id'         =>  $failrate->id,
                      'contract_id'     =>  $id,
                      'origin_portLb'   =>  $originA,
                      'origin_port'     =>  $originAIn,
                      'destiny_portLb'  =>  $destinationA,
                      'destiny_port'    =>  $destinationAIn,
                      'carrierLb'       =>  $carrierA,
                      'carrierAIn'      =>  $carrAIn,
                      'twuenty'         =>  $twuentyA,
                      'forty'           =>  $fortyA,
                      'fortyhc'         =>  $fortyhcA,
                      'currency_id'     =>  $currencyA,
                      'currencyAIn'     =>  $pruebacurre,
                      'classorigin'     =>  $classdorigin,
                      'classdestiny'    =>  $classddestination,
                      'classcarrier'    =>  $classcarrier,
                      'classtwuenty'    =>  $classtwuenty,
                      'classforty'      =>  $classforty,
                      'classfortyhc'    =>  $classfortyhc,
                      'classcurrency'   =>  $classcurrency
                     ];
            $pruebacurre = "";
            $carrAIn = "";
            $failrates->push($colec);
        }

        //------------------------------- Surcharge -----------------------------------------------------------

        $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();

        $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
        $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();

        $failsurchargecoll = collect([]);
        foreach($failsurchargeS as $failsurcharge){
            $classdorigin           =  'color:green';
            $classddestination      =  'color:green';
            $classtypedestiny       =  'color:green';
            $classcarrier           =  'color:green';
            $classsurcharger        =  'color:green';
            $classcalculationtype   =  'color:green';
            $classammount           =  'color:green';
            $classcurrency          =  'color:green';
            $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
            $originA            =  explode("_",$failsurcharge['port_orig']);
            $destinationA       =  explode("_",$failsurcharge['port_dest']);
            $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
            $ammountA           =  explode("_",$failsurcharge['ammount']);
            $currencyA          =  explode("_",$failsurcharge['currency_id']);
            $carrierA           =  explode("_",$failsurcharge['carrier_id']);
            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
            $originAIn = $originOb['id'];
            $originC   = count($originA);
            if($originC <= 1){
                $originA = $originOb['name'];
            } else{
                $originA = $originA[0].' (error)';
                $classdorigin='color:red';
            }
            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
            $destinationAIn = $destinationOb['id'];
            $destinationC   = count($destinationA);
            if($destinationC <= 1){
                $destinationA = $destinationOb['name'];
            } else{
                $destinationA = $destinationA[0].' (error)';
                $classddestination='color:red';
            }
            $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
            $surcharAin  = $surchargeOb['id'];
            $surchargeC = count($surchargeA);
            if($surchargeC <= 1){
                $surchargeA = $surchargeA[0];
            }
            else{
                $surchargeA         = $surchargeA[0].' (error)';
                $classsurcharger    = 'color:red';
            }
            $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
            $carrAIn = $carrierOb['id'];
            $carrierC = count($carrierA);
            if($carrierC <= 1){
                $carrierA = $carrierA[0];
            }
            else{
                $carrierA       = $carrierA[0].' (error)';
                $classcarrier   ='color:red';
            }
            $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
            $calculationtypeAIn = $calculationtypeOb['id'];
            $calculationtypeC   = count($calculationtypeA);
            if($calculationtypeC <= 1){
                $calculationtypeA = $calculationtypeA[0];
            }
            else{
                $calculationtypeA       = $calculationtypeA[0].' (error)';
                $classcalculationtype   = 'color:red';
            }
            $ammountC = count($ammountA);
            if($ammountC <= 1){
                $ammountA = $failsurcharge->ammount;
            }
            else{
                $ammountA       = $ammountA[0].' (error)';
                $classammount   = 'color:red';
            }

            $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
            $currencyAIn  = $currencyOb['id'];
            $currencyC    = count($currencyA);
            if($currencyC <= 1){
                $currencyA = $currencyA[0];
            }
            else{
                $currencyA      = $currencyA[0].' (error)';
                $classcurrency  = 'color:red';
            }

            $typedestinyLB    = TypeDestiny::where('description','=',$failsurcharge['typedestiny_id'])->first();

            $destinyLB        = Harbor::where('id','=',$destinationA[0])->first();
            ////////////////////////////////////////////////////////////////////////////////////
            $arreglo = [
                'failSrucharge_id'      => $failsurcharge->id,
                'surchargelb'           => $surchargeA,
                'surcharge_id'          => $surcharAin,
                'origin_portLb'         => $originA,
                'origin_port'           => $originAIn,
                'destiny_portLb'        => $destinationA,
                'destiny_port'          => $destinationAIn,
                'carrierlb'             => $carrierA,
                'carrier_id'            => $carrAIn,
                'typedestinylb'         => $typedestinyLB['description'],
                'typedestiny'           => 3,
                'ammount'               => $ammountA,
                'calculationtypelb'     => $calculationtypeA,
                'calculationtype'       => $calculationtypeAIn,
                'currencylb'            => $currencyA,
                'currency_id'           => $currencyAIn,
                'classsurcharge'        => $classsurcharger,
                'classorigin'           => $classdorigin,
                'classdestiny'          => $classddestination,
                'classtypedestiny'      => $classtypedestiny,
                'classcarrier'          => $classcarrier,
                'classcalculationtype'  => $classcalculationtype,
                'classammount'          => $classammount,
                'classcurrency'         => $classcurrency,
            ];
            //dd($arreglo);
            $failsurchargecoll->push($arreglo);
        }
        //dd($failsurchargecoll);
        //------------------------------------ Return ---------------------------------------------------------

        return  view('contracts.FailRatesSurchargerNewC',compact('rates',
                                                                 'failrates',
                                                                 'countfailrates',
                                                                 'countrates',
                                                                 'goodsurcharges',
                                                                 'failsurchargecoll',
                                                                 'countfailsurcharge',
                                                                 'countgoodsurcharge',
                                                                 'typedestiny',
                                                                 'surchargeSelect',
                                                                 'carrierSelect',
                                                                 'harbor',
                                                                 'currency',
                                                                 'calculationtypeselect',
                                                                 'id'
                                                                )); //*/
    }

    public function duplicatedContractShow($id){
        $carrier    = Carrier::pluck('name','id');
        $directions = Direction::pluck('name','id');
        $contract   = Contract::find($id);
        $contract->load('carriers');
        //dd($contract);
        return view('contracts.Body-Modals.Duplicatedscontracts',compact('contract','carrier','directions'));
    }

    public function duplicatedContractOtherCompanyShow($id,$request_dp_id){
        $carrier        = Carrier::pluck('name','id');
        $directions     = Direction::pluck('name','id');
        $contract       = Contract::find($id);
        if(!empty($contract->gp_container_id)){
            $equiment_id = $contract->gp_container_id;
        } else {
            $equiment_id = 1;
        }
        $contract->load('carriers');
        $companyUsers   = CompanyUser::pluck('name','id');
        //dd($companyUser);
        return view('contracts.Body-Modals.DuplicatedscontractsOtherCompany',compact('contract','carrier','directions','companyUsers','request_dp_id','equiment_id'));
    }

    public function duplicatedContractStore(Request $request, $id){
        $requestArray   = $request->all();
        $requestArray['requestChange'] = false;
        $data = ['id'=> $id,'data' => $requestArray];
        if(env('APP_VIEW') == 'operaciones') {
            GeneralJob::dispatch('duplicated_fcl',$data)->onQueue('operaciones');
        } else {
            GeneralJob::dispatch('duplicated_fcl',$data);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The contract is duplicating, please do not delete it');
        return redirect()->route('contracts.index');
    }

    public function duplicatedContractFromRequestStore(Request $request, $id){
        $requestArray   = $request->all();
        //dd($requestArray);
        $requestArray['requestChange'] 	= true;
        $time   = new \DateTime();
        $now2   = $time->format('Y-m-d H:i:s');
        $requestFc = NewContractRequest::find($requestArray['request_id']);

        if(empty($requestFc->contract_id) != true){
            $contracOld = Contract::find($requestFc->contract_id);
            if(empty($contracOld->id) != true){
                $contracOld->delete();
            }
        }

        if($requestFc->username_load == 'Not assigned'){
            $requestFc->username_load = \Auth::user()->name.' '.\Auth::user()->lastname;
        }
        $requestFc->status        = 'Processing';
        if($requestFc->time_star_one == false){
            $requestFc->time_star       = $now2;
            $requestFc->time_star_one   = true;
        }
        $requestFc->update();
        $data           = ['id'=> $id,'data' => $requestArray];
        //dd($data);
        if(env('APP_VIEW') == 'operaciones') {
            GeneralJob::dispatch('duplicated_fcl',$data)->onQueue('operaciones');
        } else {
            GeneralJob::dispatch('duplicated_fcl',$data);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The contract is duplicating, please do not delete it');
        return redirect()->back();
    }

    public function selectRequest(Request $request){
        $data = [];
        if($request->has('q')){
            $search = $request->q;
            if($search != null){
                $data = NewContractRequest::where('id','like','%'.$search.'%')->get();
            }
        }
        return response()->json($data);
    }


}