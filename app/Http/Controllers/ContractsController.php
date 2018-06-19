<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contract;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\FailRate;
use App\Currency;
use App\CalculationType;
use App\LocalCharge;
use App\Surcharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\TypeDestiny;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UploadFileRateRequest;

class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $contracts = Contract::where('user_id','=',Auth::user()->id)->with('rates')->get();
        //$contracts->rates;
        //dd($contracts);
        /* foreach ($contracts as $arr) {
            foreach ($arr->rates as $rates) {
                echo $arr->name."  ".$rates->port_origin->name."<br>";

            }
        }*/
        return view('contracts/index', ['arreglo' => $contracts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function add()
    {

        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();


        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');


        return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $contract = new Contract($request->all());
        $contract->user_id =Auth::user()->id;
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->save();

        $details = $request->input('origin_id');
        $detailscharges = $request->input('type');
        // For Each de los rates 
        $contador = 1;
        foreach($details as $key => $value)
        {
            if(!empty($request->input('twuenty.'.$key))) {


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
                $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();    


                $detailportOrig = $request->input('port_origlocal'.$contador);
                $detailportDest = $request->input('port_destlocal'.$contador);


                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {


                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;

            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->action('ContractsController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $contracts = Contract::with('rates')->get();


        $contracts = Contract::with('rates','localcharges.localcharports','localcharges.localcharcarriers')->get()->find($id);

        $objtypedestiny = new TypeDestiny();
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();

        $harbor = $objharbor->all()->pluck('name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('user_id','=',Auth::user()->id)->pluck('name','id');

        return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','id'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $requestForm = $request->all();
        $contract = Contract::find($id);
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->update($requestForm);

        $details = $request->input('origin_id');
        $detailscharges = $request->input('ammount');
        $contador = 1;
        // for each rates 
        foreach($details as $key => $value)
        {
            if(!empty($request->input('twuenty.'.$key))) {


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
                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {


                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;

            }


        }


        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully update this contract.');

        return redirect()->action('ContractsController@index');


    }



    public function updateRates(Request $request, $id)
    {
        //dd("imi here");
        $requestForm = $request->all();

        $rate = Rate::find($id);
        $rate->update($requestForm);

    }

    public function UploadFileRateForContract(Request $request){

        try {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            $validator = \Validator::make(
                array('ext' => $ext),
                array('ext' => 'in:xls,xlsx,csv')
            );

            if ($validator->fails()) {
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
                return redirect()->route('contracts.edit',$request->contract_id);
            }

            //obtenemos el nombre del archivo
            $nombre = $file->getClientOriginalName();

            $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
            //dd(\Storage::disk('UpLoadFile')->url($nombre));

            $contract = $request->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors) {
                foreach ($reader->get() as $book) {

                    $origB=false;
                    $destiB=false;
                    $carriB=false;
                    $twuentyB=false;
                    $fortyB=false;
                    $fortyhcB=false;
                    $curreB=false;
                    
                    $originV;
                    $destinationV;
                    $carrierV;
                    $twuentyV;
                    $fortyV;
                    $fortyhcV;
                    $currencyV;
                    
                    $currenc = Currency::where('alphacode','=',$book->currency)->first();
                    $carrier = Carrier::where('name','=',$book->carrier)->first();

                    if(empty($book->origin) != true){
                        $origB=true;
                        $originV = $book->origin;
                    }else{
                        $originV = $book->origin.'_E';
                    }

                    if(empty($book->destination) != true ){
                        $destiB=true;
                        $destinationV = $book->destination;
                    }else{
                        $destinationV = $book->destination.'_E';
                    }
                    
                    if(empty($carrier->id) != true){
                        $carriB=true;
                        $carrierV = $carrier->id;
                    }else{
                        $carrierV = $book->carrier.'_E';
                    }

                    if(empty($book->twuenty) != true ){
                        $twuentyB=true;
                        $twuentyV = $book->twuenty;
                    }
                    else{
                        $twuentyV = $book->twuenty.'_E';
                    }
                    
                    if(empty($book->forty) != true ){
                        $fortyB=true;
                        $fortyV = $book->forty;
                    }
                    else{
                        $fortyV = $book->forty.'_E';
                    }
                    
                    if(empty($book->fortyhc) != true ){
                        $fortyhcB=true;
                        $fortyhcV = $book->fortyhc;
                    }
                    else{
                        $fortyhcV = $book->fortyhc.'_E';
                    }
                    
                    if(empty($currenc->id) != true){
                        $curreB=true;
                        $currencyV =  $currenc->id;
                    }
                    else{
                        $currencyV = $book->currency.'_E';
                    }

                    if( $origB == true && $destiB == true
                       && $carriB == true && $twuentyB == true
                       && $fortyB == true && $fortyhcB == true
                       && $curreB == true ) {

                        Rate::create([
                            'origin_port'   => $originV,
                            'destiny_port'  => $destinationV,
                            'carrier_id'    => $carrierV,
                            'contract_id'   => $contract,
                            'twuenty'       => $twuentyV,
                            'forty'         => $fortyV,
                            'fortyhc'       => $fortyhcV,
                            'currency_id'   => $currencyV,
                        ]);
                    }
                    else{

                        FailRate::create([
                            'origin_port'   => $originV,
                            'destiny_port'  => $destinationV,
                            'carrier_id'    => $carrierV,
                            'contract_id'   => $contract,
                            'twuenty'       => $twuentyV,
                            'forty'         => $fortyV,
                            'fortyhc'       => $fortyhcV,
                            'currency_id'   => $currencyV,
                        ]);
                        $errors++;
                        
                    }
                }
            });
            if($errors <= 0){
                $request->session()->flash('message.nivel', 'success');
                $request->session()->flash('message.title', 'Well done!');
                $request->session()->flash('message.content', 'You successfully added the rate ');
            }
            else{
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.title', 'Well done!');
                if($errors == 1){
                    $request->session()->flash('message.content', $errors.' fee is not charged correctly');
                }else{
                    $request->session()->flash('message.content', $errors.' Rates did not load correctly');
                }
            }
            return redirect()->route('contracts.edit',$request->contract_id);
            //dd($res);

        } catch (\Illuminate\Database\QueryException $e) {

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('contracts.edit',$request->contract_id);
        }
    }

    public function updateLocalChar(Request $request, $id)
    {


        $localC = LocalCharge::find($id);
        $localC->surcharge_id = $request->input('surcharge_id');
        $localC->typedestiny_id  = $request->input('changetype');
        $localC->calculationtype_id = $request->input('calculationtype_id');
        $localC->ammount = $request->input('ammount');
        $localC->currency_id = $request->input('currency_id');
        $localC->update();

        $detailportOrig = $request->input('port_origlocal');
        $detailportDest = $request->input('port_destlocal');
        $carrier = $request->input('carrier_id');
        $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPort::where("localcharge_id",$id);
        $deletePort->delete();

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
        foreach($carrier as $key)
        {
            $detailcarrier = new LocalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->localcharge_id = $id;
            $detailcarrier->save();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rate = Rate::find($id);
        $rate->delete();
        return $rate;
    }

    public function destroyLocalCharges($id)
    {

        $local = LocalCharge::find($id);
        $local->delete();

    }
    public function destroyRates(Request $request,$id)
    {

        $rates = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete the rate ');
        return redirect()->action('ContractsController@index');

    }

    public function destroymsg($id)
    {
        return view('contracts/message' ,['rate_id' => $id]);

    }
}
