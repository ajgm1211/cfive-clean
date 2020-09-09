<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\Carrier;
use App\CompanyUser;
use App\Country;
use App\Currency;
use App\GlobalCharCarrier;
use App\GlobalCharCountry;
use App\GlobalCharCountryException;
use App\GlobalCharCountryPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharPortCountry;
use App\GlobalCharPortException;
use App\Harbor;
use App\Jobs\GlobalchargerDuplicateFclLclJob as GCDplFclLcl;
use App\Region;
use App\RegionPt;
use App\Surcharge;
use App\TypeDestiny;
use App\ViewGlobalCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Validator;
//inclui

class GlobalChargesController extends Controller
{
    public function index()
    {
        $company_userid = \Auth::user()->company_user_id;
        return view('globalcharges.indexTw', compact('company_userid'));
    }

    public function create()
    {
        //
    }

    public function store(request $request)
    { // cambio de request

        // PORT TO PORT
        if ($request->input('allOriginPort') != null) {
            $all_port = array($request->input('allOriginPort'));
            $request->request->add(['port_orig' => $all_port]);
        }
        if ($request->input('allDestinationPort') != null) {
            $all_portD = array($request->input('allDestinationPort'));
            $request->request->add(['port_dest' => $all_portD]);
        }
        //COUNTRY TO COUNTRY
        if ($request->input('allOriginCountry') != null) {
            $all_country = array($request->input('allOriginCountry'));
            $request->request->add(['country_orig' => $all_country]);
        }

        if ($request->input('allDestinationCountry') != null) {
            $all_countryD = array($request->input('allDestinationCountry'));
            $request->request->add(['country_dest' => $all_countryD]);
        }

        //PORT TO COUNTRY

        if ($request->input('allOriginPortCountry') != null) {
            $all_country = array($request->input('allOriginPortCountry'));
            $request->request->add(['portcountry_orig' => $all_country]);
        }

        if ($request->input('allDestinationPortCountry') != null) {
            $all_countryD = array($request->input('allDestinationPortCountry'));
            $request->request->add(['portcountry_dest' => $all_countryD]);
        }

        //COUNTRY  TO PORT 


        if ($request->input('allOriginCountryPort') != null) {
            $all_country = array($request->input('allOriginCountryPort'));
            $request->request->add(['countryport_orig' => $all_country]);
        }

        if ($request->input('allDestinationCountryPort') != null) {
            $all_countryD = array($request->input('allDestinationCountryPort'));
            $request->request->add(['countryport_dest' => $all_countryD]);
        }

        $data = $this->validateData($request);
        // $request->validated();
        $detailscharges = $request->input('type');
        $calculation_type = $request->input('calculationtype');

        //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
        $detailcarrier = $request->input('localcarrier');
        foreach ($detailcarrier as $c => $carrier) {
            foreach ($calculation_type as $ct => $ctype) {

                $global = new GlobalCharge();
                $validation = explode('/', $request->validation_expire);
                $global->validity = $validation[0];
                $global->expire = $validation[1];
                $global->surcharge_id = $request->input('type');
                $global->typedestiny_id = $request->input('changetype');
                $global->calculationtype_id = $ctype;
                $global->ammount = $request->input('ammount');
                $global->currency_id = $request->input('localcurrency_id');
                $global->company_user_id = Auth::user()->company_user_id;
                $global->save();
                // Detalles de puertos y carriers
                //$totalCarrier = count($request->input('localcarrier'.$contador));
                //$totalport =  count($request->input('port_id'.$contador));

                $detailcarrier = new GlobalCharCarrier();
                $detailcarrier->carrier_id = $carrier;
                $detailcarrier->globalcharge()->associate($global);
                $detailcarrier->save();

                $typerate = $request->input('typeroute');

                if ($typerate == 'port') {
                    $detailport = $request->input('port_orig');
                    $detailportDest = $request->input('port_dest');
                    //Excepciones

                    foreach ($detailport as $p => $value) {
                        foreach ($detailportDest as $dest => $valuedest) {
                            $ports = new GlobalCharPort();
                            $ports->port_orig = $value;
                            $ports->port_dest = $valuedest;
                            $ports->typedestiny_id = $request->input('changetype');
                            $ports->globalcharge()->associate($global);
                            $ports->save();
                        }
                    }
                } elseif ($typerate == 'country') {
                    $detailCountrytOrig = $request->input('country_orig');
                    $detailCountryDest = $request->input('country_dest');
                    foreach ($detailCountrytOrig as $p => $valueC) {
                        foreach ($detailCountryDest as $dest => $valuedestC) {
                            $detailcountry = new GlobalCharCountry();
                            $detailcountry->country_orig = $valueC;
                            $detailcountry->country_dest = $valuedestC;
                            $detailcountry->globalcharge()->associate($global);
                            $detailcountry->save();
                        }
                    }
                } elseif ($typerate == 'portcountry') {
                    $detailPortCountrytOrig = $request->input('portcountry_orig');
                    $detailPortCountryDest = $request->input('portcountry_dest');
                    foreach ($detailPortCountrytOrig as $p => $valuePCorig) {
                        foreach ($detailPortCountryDest as $dest => $valuePCdest) {
                            $detail = new GlobalCharPortCountry();
                            $detail->port_orig = $valuePCorig;
                            $detail->country_dest = $valuePCdest;
                            $detail->globalcharge()->associate($global);
                            $detail->save();
                        }
                    }
                } elseif ($typerate == 'countryport') {
                    $detailCountryPortOrig = $request->input('countryport_orig');
                    $detailCountryPortDest = $request->input('countryport_dest');
                    foreach ($detailCountryPortOrig as $p => $valueCPorig) {
                        foreach ($detailCountryPortDest as $dest => $valueCPdest) {
                            $detail = new GlobalCharCountryPort();
                            $detail->country_orig = $valueCPorig;
                            $detail->port_dest = $valueCPdest;
                            $detail->globalcharge()->associate($global);
                            $detail->save();
                        }
                    }
                }
                //Excepciones Ports
                if ($request->input('exceptionPortOrig') != null) {
                    $exceptionPortOrig = $request->input('exceptionPortOrig');
                    foreach ($exceptionPortOrig as $keyPortOrig => $exPortOrig) {
                        $ports = new GlobalCharPortException();
                        $ports->port_orig = $exPortOrig;

                        $ports->globalcharge()->associate($global);
                        $ports->save();
                    }
                }

                if ($request->input('exceptionPortDest') != null) {
                    $exceptionPortDest = $request->input('exceptionPortDest');
                    foreach ($exceptionPortDest as $keyPortDest => $exPortDest) {
                        $ports = new GlobalCharPortException();

                        $ports->port_dest = $exPortDest;
                        $ports->globalcharge()->associate($global);
                        $ports->save();
                    }
                }

                // Excepciones Country
                if ($request->input('exceptionCountryOrig') != null) {
                    $exceptionCountryOrig = $request->input('exceptionCountryOrig');
                    foreach ($exceptionCountryOrig as $keyCountOrig => $exCountOrig) {
                        $countries = new GlobalCharCountryException();
                        $countries->country_orig = $exCountOrig;

                        $countries->globalcharge()->associate($global);
                        $countries->save();
                    }
                }

                if ($request->input('exceptionCountryDest') != null) {
                    $exceptionCountryDest = $request->input('exceptionCountryDest');
                    foreach ($exceptionCountryDest as $keyCountDest => $exCountDest) {
                        $countries = new GlobalCharCountryException();

                        $countries->country_dest = $exCountDest;
                        $countries->globalcharge()->associate($global);
                        $countries->save();
                    }
                }
            }
        }
        // EVENTO INTERCOM

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register created successfully!');
        return redirect()->action('GlobalChargesController@index');
    }
    
    public function validateData($request)
    {

        //PORT TO PORT
        if ($request->input('allOriginPort') != null && $request->input('typeroute') == 'port') {

            $vdata = [
                'port_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } else if ($request->input('allDestinationPort') != null && $request->input('typeroute') == 'port') {

            $vdata = [
                'port_orig' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginPort') != null && $request->input('allDestinationPort') != null && $request->input('typeroute') == 'port') {

            $vdata = [
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginPort') == null && $request->input('allDestinationPort') == null && $request->input('typeroute') == 'port') {

            $vdata = [
                'port_dest' => 'required',
                'port_orig' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        }
        //COUNTRY TO COUNTRY
        elseif ($request->input('allOriginCountry') != null && $request->input('typeroute') == 'country') {

            $vdata = [
                'country_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allDestinationCountry') != null && $request->input('typeroute') == 'country') {

            $vdata = [
                'country_orig' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginCountry') != null && $request->input('allDestinationCountry') != null && $request->input('typeroute') == 'country') {

            $vdata = [
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginCountry') == null && $request->input('allDestinationCountry') == null && $request->input('typeroute') == 'country') {

            $vdata = [
                'country_orig' => 'required',
                'country_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        }
        //PORT TO COUNTRY    
        elseif ($request->input('allOriginPortCountry') != null && $request->input('typeroute') == 'portcountry') {

            $vdata = [
                'portcountry_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allDestinationPortCountry') != null && $request->input('typeroute') == 'portcountry') {

            $vdata = [
                'portcountry_orig' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginPortCountry') != null && $request->input('allDestinationPortCountry') != null && $request->input('typeroute') == 'portcountry') {

            $vdata = [
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginPortCountry') == null && $request->input('allDestinationPortCountry') == null && $request->input('typeroute') == 'portcountry') {

            $vdata = [
                'portcountry_orig' => 'required',
                'portcountry_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        }
        //COUNTRY TO PORT    
        elseif ($request->input('allOriginCountryPort') != null && $request->input('typeroute') == 'countryport') {

            $vdata = [
                'countryport_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allDestinationCountryPort') != null && $request->input('typeroute') == 'countryport') {

            $vdata = [
                'countryport_orig' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } elseif ($request->input('allOriginCountryPort') != null && $request->input('allDestinationCountryPort') != null && $request->input('typeroute') == 'countryport') {

            $vdata = [
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        } else {

            $vdata = [
                'countryport_orig' => 'required',
                'countryport_dest' => 'required',
                'type' => 'required',
                'calculationtype' => 'required',
                'localcarrier' => 'required',
            ];
        }

        $validator = \Validator::make($request->all(), $vdata);

        return $validator->validate();
    }

    public function updateGlobalChar(request $request, $id)
    {

        //        $objcurrency    = new Currency();
        //        $objsurcharge   = new Surcharge();
        //
        //        $harbor         = Harbor::all()->pluck('display_name','id');
        //        $carrier        = Carrier::all()->pluck('name','id');
        //        $currency       = Currency::all()->pluck('alphacode','id');
        //        $calculationT   = CalculationType::all()->pluck('name','id');
        //        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        //dd($request);
        /* $type =  TypeDestiny::all();
        $changetype = $type->find($request->input('changetype'))->toArray();*/

        // PORT TO PORT
        if ($request->input('allOriginPort') != null) {
            $all_port = array($request->input('allOriginPort'));
            $request->request->add(['port_orig' => $all_port]);
        }
        if ($request->input('allDestinationPort') != null) {
            $all_portD = array($request->input('allDestinationPort'));
            $request->request->add(['port_dest' => $all_portD]);
        }
        //COUNTRY TO COUNTRY
        if ($request->input('allOriginCountry') != null) {
            $all_country = array($request->input('allOriginCountry'));
            $request->request->add(['country_orig' => $all_country]);
        }

        if ($request->input('allDestinationCountry') != null) {
            $all_countryD = array($request->input('allDestinationCountry'));
            $request->request->add(['country_dest' => $all_countryD]);
        }

        //PORT TO COUNTRY

        if ($request->input('allOriginPortCountry') != null) {
            $all_country = array($request->input('allOriginPortCountry'));
            $request->request->add(['portcountry_orig' => $all_country]);
        }

        if ($request->input('allDestinationPortCountry') != null) {
            $all_countryD = array($request->input('allDestinationPortCountry'));
            $request->request->add(['portcountry_dest' => $all_countryD]);
        }

        //COUNTRY  TO PORT 


        if ($request->input('allOriginCountryPort') != null) {
            $all_country = array($request->input('allOriginCountryPort'));
            $request->request->add(['countryport_orig' => $all_country]);
        }

        if ($request->input('allDestinationCountryPort') != null) {
            $all_countryD = array($request->input('allDestinationCountryPort'));
            $request->request->add(['countryport_dest' => $all_countryD]);
        }





        $global = GlobalCharge::find($id);
        $validation = explode('/', $request->validation_expire);
        $global->validity = $validation[0];
        $global->expire = $validation[1];
        $global->surcharge_id = $request->input('surcharge_id');
        $global->typedestiny_id = $request->input('changetype');
        $global->calculationtype_id = $request->input('calculationtype_id');
        $global->ammount = $request->input('ammount');
        $global->currency_id = $request->input('currency_id');
        $global->update();

        $carrier = $request->input('carrier_id');
        $deleteCarrier = GlobalCharCarrier::where("globalcharge_id", $id);
        $deleteCarrier->delete();
        $deletePort = GlobalCharPort::where("globalcharge_id", $id);
        $deletePort->delete();
        $deleteCountry = GlobalCharCountry::where("globalcharge_id", $id);
        $deleteCountry->delete();

        $deletePortCountry = GlobalCharPortCountry::where("globalcharge_id", $id);
        $deletePortCountry->delete();

        $deleteCountryPort = GlobalCharCountryPort::where("globalcharge_id", $id);
        $deleteCountryPort->delete();

        // Excepciones 
        $deletePortExcepcion = GlobalCharPortException::where("globalcharge_id", $id);
        $deletePortExcepcion->delete();

        $deleteCountryExcepcion = GlobalCharCountryException::where("globalcharge_id", $id);
        $deleteCountryExcepcion->delete();



        $contador = 1;
        $company_user = $global->company_user_id;
        foreach ($carrier as $key) {
            if ($contador > 1) {
                $global = null;
                $id = null;
                $global = GlobalCharge::create([
                    'validity' => $validation[0],
                    'expire' => $validation[1],
                    'surcharge_id' => $request->input('surcharge_id'),
                    'typedestiny_id' => $request->input('changetype'),
                    'calculationtype_id' => $request->input('calculationtype_id'),
                    'ammount' => $request->input('ammount'),
                    'company_user_id' => $company_user,
                    'currency_id' => $request->input('currency_id'),
                ]);
                $id = $global->id;
            }
            $typerate = $request->input('typeroute');
            if ($typerate == 'port') {
                $port_orig = $request->input('port_orig');
                $port_dest = $request->input('port_dest');
                foreach ($port_orig as $orig => $valueorig) {
                    foreach ($port_dest as $dest => $valuedest) {
                        $detailport = new GlobalCharPort();
                        $detailport->port_orig = $valueorig;
                        $detailport->port_dest = $valuedest;
                        $detailport->typedestiny_id = $request->input('changetype');
                        $detailport->globalcharge_id = $id;
                        $detailport->save();
                    }
                }
            } elseif ($typerate == 'country') {

                $detailCountrytOrig = $request->input('country_orig');
                $detailCountryDest = $request->input('country_dest');
                foreach ($detailCountrytOrig as $p => $valueC) {
                    foreach ($detailCountryDest as $dest => $valuedestC) {
                        $detailcountry = new GlobalCharCountry();
                        $detailcountry->country_orig = $valueC;
                        $detailcountry->country_dest = $valuedestC;
                        $detailcountry->globalcharge()->associate($global);
                        $detailcountry->save();
                    }
                }
            } elseif ($typerate == 'portcountry') {
                $detailPortCountrytOrig = $request->input('portcountry_orig');
                $detailPortCountryDest = $request->input('portcountry_dest');
                foreach ($detailPortCountrytOrig as $p => $valuePCorig) {
                    foreach ($detailPortCountryDest as $dest => $valuePCdest) {
                        $detail = new GlobalCharPortCountry();
                        $detail->port_orig = $valuePCorig;
                        $detail->country_dest = $valuePCdest;
                        $detail->globalcharge()->associate($global);
                        $detail->save();
                    }
                }
            } elseif ($typerate == 'countryport') {
                $detailCountryPortOrig = $request->input('countryport_orig');
                $detailCountryPortDest = $request->input('countryport_dest');
                foreach ($detailCountryPortOrig as $p => $valueCPorig) {
                    foreach ($detailCountryPortDest as $dest => $valueCPdest) {
                        $detail = new GlobalCharCountryPort();
                        $detail->country_orig = $valueCPorig;
                        $detail->port_dest = $valueCPdest;
                        $detail->globalcharge()->associate($global);
                        $detail->save();
                    }
                }
            }

            //Excepciones Ports
            if ($request->input('exceptionPortOrig') != null) {
                $exceptionPortOrig = $request->input('exceptionPortOrig');
                foreach ($exceptionPortOrig as $keyPortOrig => $exPortOrig) {
                    $ports = new GlobalCharPortException();
                    $ports->port_orig = $exPortOrig;

                    $ports->globalcharge()->associate($global);
                    $ports->save();
                }
            }

            if ($request->input('exceptionPortDest') != null) {
                $exceptionPortDest = $request->input('exceptionPortDest');
                foreach ($exceptionPortDest as $keyPortDest => $exPortDest) {
                    $ports = new GlobalCharPortException();

                    $ports->port_dest = $exPortDest;
                    $ports->globalcharge()->associate($global);
                    $ports->save();
                }
            }

            // Excepciones Country
            if ($request->input('exceptionCountryOrig') != null) {
                $exceptionCountryOrig = $request->input('exceptionCountryOrig');
                foreach ($exceptionCountryOrig as $keyCountOrig => $exCountOrig) {
                    $countries = new GlobalCharCountryException();
                    $countries->country_orig = $exCountOrig;

                    $countries->globalcharge()->associate($global);
                    $countries->save();
                }
            }

            if ($request->input('exceptionCountryDest') != null) {
                $exceptionCountryDest = $request->input('exceptionCountryDest');
                foreach ($exceptionCountryDest as $keyCountDest => $exCountDest) {
                    $countries = new GlobalCharCountryException();

                    $countries->country_dest = $exCountDest;
                    $countries->globalcharge()->associate($global);
                    $countries->save();
                }
            }

            $detailcarrier = new GlobalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->globalcharge_id = $id;
            $detailcarrier->save();
            $contador = $contador + 1;
        }

        /*
        $global =  GlobalCharge::whereHas('companyUser', function($q) {
        $q->where('company_user_id', '=', Auth::user()->company_user_id);
        })->with('globalcharport.portOrig','globalcharport.portDest','GlobalCharCarrier.carrier','typedestiny')->get();

        return view('globalcharges/index', compact('global','carrier','harbor','currency','calculationT','surcharge','typedestiny'));*/
        return redirect()->back()->with('globalchar', 'true');
    }

    public function destroyGlobalCharges($id)
    {

        $global = GlobalCharge::find($id);
        $global->delete();
    }
    public function editGlobalChar($id)
    {
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objtypedestiny = new TypeDestiny();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $countries = Country::pluck('name', 'id');
        $route = 'update-global-charge';

        $calculationT = $objcalculation->all()->pluck('name', 'id');
        $typedestiny = $objtypedestiny->all()->pluck('description', 'id');
        $surcharge = $objsurcharge->where('company_user_id', '=', Auth::user()->company_user_id)->pluck('name', 'id');
        $harbor = $objharbor->all()->pluck('display_name', 'id');
        $carrier = $objcarrier->all()->pluck('name', 'id');
        $currency = $objcurrency->all()->pluck('alphacode', 'id');
        $globalcharges = GlobalCharge::find($id);
        $validation_expire = $globalcharges->validity . " / " . $globalcharges->expire;
        $globalcharges->setAttribute('validation_expire', $validation_expire);
        $amount = $globalcharges->amount;

        $activacion = array("rdrouteP" => false, "rdrouteC" => false, "rdroutePC" => false, "rdrouteCP" => false, 'act' => '');

        if (!$globalcharges->globalcharcountry->isEmpty()) {
            $activacion['rdrouteC'] = true;
            $activacion['act'] = 'divcountry';
        }
        if (!$globalcharges->globalcharportcountry->isEmpty()) {
            $activacion['rdroutePC'] = true;
            $activacion['act'] = 'divportcountry';
        }
        if (!$globalcharges->globalcharcountryport->isEmpty()) {
            $activacion['rdrouteCP'] = true;
            $activacion['act'] = 'divcountryport';
        }
        if (!$globalcharges->globalcharport->isEmpty()) {
            $activacion['rdrouteP'] = true;
            $activacion['act'] = 'divport';
        }

        //Exepciones 

        //dd($globalcharges->globalexceptioncountry->pluck('country_orig')->unique()->pluck('country_orig'));

        //dd($activacion);

        return view('globalcharges.edit', compact('globalcharges', 'harbor', 'carrier', 'currency', 'calculationT', 'typedestiny', 'surcharge', 'countries', 'activacion', 'route', 'amount'));
    }

    public function addGlobalChar()
    {

        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objtypedestiny = new TypeDestiny();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $countries = Country::pluck('name', 'id');
        $route = 'globalcharges.store';

        $calculationT = $objcalculation->all()->pluck('name', 'id');
        $typedestiny = $objtypedestiny->all()->pluck('description', 'id');
        $surcharge = $objsurcharge->where('company_user_id', '=', Auth::user()->company_user_id)->pluck('name', 'id');
        $harbor = $objharbor->all()->pluck('display_name', 'id');
        $carrier = $objcarrier->all()->pluck('name', 'id');
        $currency = $objcurrency->all()->pluck('alphacode', 'id');
        $company_user = CompanyUser::find(\Auth::user()->company_user_id);
        $currency_cfg = Currency::find($company_user->currency_id);

        return view('globalcharges.add', compact('harbor', 'carrier', 'currency', 'calculationT', 'typedestiny', 'surcharge', 'countries', 'currency_cfg', 'route'));
    }

    public function duplicateGlobalCharges($id)
    {



        $countries = Country::pluck('name', 'id');
        $calculationT = CalculationType::all()->pluck('name', 'id');
        $typedestiny = TypeDestiny::all()->pluck('description', 'id');
        $surcharge = Surcharge::where('company_user_id', '=', Auth::user()->company_user_id)->pluck('name', 'id');
        $harbor = Harbor::all()->pluck('display_name', 'id');
        $carrier = Carrier::all()->pluck('name', 'id');
        $currency = Currency::all()->pluck('alphacode', 'id');
        $globalcharges = GlobalCharge::find($id);
        $validation_expire = $globalcharges->validity . " / " . $globalcharges->expire;
        $globalcharges->setAttribute('validation_expire', $validation_expire);
        $activacion = array("rdrouteP" => false, "rdrouteC" => false, "rdroutePC" => false, "rdrouteCP" => false, 'act' => '');

        if (!$globalcharges->globalcharcountry->isEmpty()) {
            $activacion['rdrouteC'] = true;
            $activacion['act'] = 'divcountry';
        }
        if (!$globalcharges->globalcharportcountry->isEmpty()) {
            $activacion['rdroutePC'] = true;
            $activacion['act'] = 'divportcountry';
        }
        if (!$globalcharges->globalcharcountryport->isEmpty()) {
            $activacion['rdrouteCP'] = true;
            $activacion['act'] = 'divcountryport';
        }
        if (!$globalcharges->globalcharport->isEmpty()) {
            $activacion['rdrouteP'] = true;
            $activacion['act'] = 'divport';
        }
        return view('globalcharges.duplicate', compact('globalcharges', 'harbor', 'carrier', 'currency', 'calculationT', 'typedestiny', 'surcharge', 'countries', 'activacion'));
    }

    public function show($id)
    {
        $globalcharges = DB::select('call  select_for_company_globalcharger(' . $id . ')');

        return DataTables::of($globalcharges)
            ->editColumn('surchargelb', function ($globalcharges) {
                return $globalcharges->surcharges;
            })
            ->editColumn('origin_portLb', function ($globalcharges) {
                if (empty($globalcharges->port_orig) != true) {
                    return $globalcharges->port_orig;
                } else if (empty($globalcharges->country_orig) != true) {
                    return $globalcharges->country_orig;
                } else if (empty($globalcharges->portcountry_orig) != true) {
                    return $globalcharges->portcountry_orig;
                } else if (empty($globalcharges->countryport_orig) != true) {
                    return $globalcharges->countryport_orig;
                }
            })
            ->editColumn('destiny_portLb', function ($globalcharges) {
                if (empty($globalcharges->port_dest) != true) {
                    return $globalcharges->port_dest;
                } else if (empty($globalcharges->country_dest) != true) {
                    return $globalcharges->country_dest;
                } else if (empty($globalcharges->portcountry_dest) != true) {
                    return $globalcharges->portcountry_dest;
                } else if (empty($globalcharges->countryport_dest) != true) {
                    return $globalcharges->countryport_dest;
                }
            })
            ->editColumn('typedestinylb', function ($globalcharges) {
                return $globalcharges->typedestiny;
            })
            ->editColumn('calculationtypelb', function ($globalcharges) {
                return $globalcharges->calculationtype;
            })
            ->editColumn('currencylb', function ($globalcharges) {
                return $globalcharges->currency;
            })
            ->editColumn('carrierlb', function ($globalcharges) {
                return $globalcharges->carrier;
            })
            ->editColumn('validitylb', function ($globalcharges) {
                return $globalcharges->validity . '/' . $globalcharges->expire;
            })
            ->addColumn('action', function ($globalcharges) {
                return '
                    <a  id="edit_l" onclick="AbrirModal(' . "'editGlobalCharge'" . ',' . $globalcharges->id . ')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l' . $globalcharges->id . '" class="la la-times-circle"></i>
										</a>

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal(' . "'duplicateGlobalCharge'" . ',' . $globalcharges->id . ')">
											<i class="la la-plus"></i>
										</a>
                    ';
            })
            ->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            ->rawColumns(['checkbox', 'action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
    }

    public function destroyArr(Request $request)
    {
        $globals_id_array = $request->input('id');
        $global = GlobalCharge::whereIn('id', $globals_id_array);
        if ($global->delete()) {
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '2']);
        }
    }

    // CRUD Administarator -----------------------------------------------------------------------------------------------------

    public function loadSelectForRegion(Request $request)
    {

        $typeRoute = $request->typeRoute;
        $typeSetect = $request->typeSelect;
        $dataPCR = [];
        $data = collect();
        $difer = null;

        if ($typeRoute == 'port') {
            $difer = 'port';
            if ($typeSetect == 'origin') {
                $dataPCR = $request->origen_port_reg;
            } else if ($typeSetect == 'destiny') {
                $dataPCR = $request->destino_port_reg;
            }
        } else if ($typeRoute == 'country') {
            $difer = 'country';
            if ($typeSetect == 'origin') {
                $dataPCR = $request->origen_count_reg;
            } else if ($typeSetect == 'destiny') {
                $dataPCR = $request->destino_count_reg;
            }
        } else if ($typeRoute == 'portcountry') {
            if ($typeSetect == 'origin') {
                $difer = 'port';
                $dataPCR = $request->origen_port_reg;
            } else if ($typeSetect == 'destiny') {
                $difer = 'country';
                $dataPCR = $request->destino_count_reg;
            }
        } else if ($typeRoute == 'countryport') {
            if ($typeSetect == 'origin') {
                $difer = 'country';
                $dataPCR = $request->origen_count_reg;
            } else if ($typeSetect == 'destiny') {
                $difer = 'port';
                $dataPCR = $request->destino_port_reg;
            }
        }

        if (!empty($dataPCR)) {
            if ($difer == 'port') {
                $dataR = RegionPt::whereIn('id', $dataPCR)->get();
                if (!empty($dataR)) {
                    $dataR->load('PortRegions');
                    foreach ($dataR as $rr) {
                        foreach ($rr->PortRegions->pluck('harbor_id') as $values) {
                            $data->push($values);
                        }
                    }
                }
            } elseif ($difer == 'country') {
                $dataR = Region::whereIn('id', $dataPCR)->get();
                if (!empty($dataR)) {
                    $dataR->load('CountriesRegions');
                    foreach ($dataR as $rr) {
                        foreach ($rr->CountriesRegions->pluck('country_id') as $values) {
                            $data->push($values);
                        }
                    }
                }
            }
        }

        if (!empty($data)) {
            $data = $data->toArray();
            $dataUn = array_unique($data);
            $data = [];
            foreach ($dataUn as $dataRe) {
                array_push($data, $dataRe);
            }
        }

        return response()->json(['success' => 'ok', 'data' => ['select' => $typeSetect, 'typeValues' => $difer, 'type' => $typeRoute, 'values' => $data]]);
    }

    public function indexAdm(Request $request)
    {
        $companies = CompanyUser::pluck('name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        return view('globalchargesAdm.index', compact('companies', 'carriers', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function createAdm(Request $request)
    {
        $globalcharges = ViewGlobalCharge::select(['id', 'charge', 'charge_type', 'calculation_type', 'origin_port', 'origin_country', 'destination_port', 'destination_country', 'carrier', 'amount', 'currency_code', 'valid_from', 'valid_until', 'company_user', 'portcountry_orig', 'portcountry_dest', 'countryport_orig', 'countryport_dest'])->companyUser($request->company_id)->carrier($request->carrier);

        return DataTables::of($globalcharges)
            ->editColumn('surchargelb', function ($globalcharges) {
                return $globalcharges->charge;
            })
            ->editColumn('origin_portLb', function ($globalcharges) {
                if (empty($globalcharges->origin_port) != true) {
                    return $globalcharges->origin_port;
                } else if (empty($globalcharges->origin_country) != true) {
                    return $globalcharges->origin_country;
                } else if (empty($globalcharges->portcountry_orig) != true) {
                    return $globalcharges->portcountry_orig;
                } else if (empty($globalcharges->countryport_orig) != true) {
                    return $globalcharges->countryport_orig;
                }
            })
            ->editColumn('destiny_portLb', function ($globalcharges) {
                if (empty($globalcharges->destination_port) != true) {
                    return $globalcharges->destination_port;
                } else if (empty($globalcharges->destination_country) != true) {
                    return $globalcharges->destination_country;
                } else if (empty($globalcharges->portcountry_dest) != true) {
                    return $globalcharges->portcountry_dest;
                } else if (empty($globalcharges->countryport_dest) != true) {
                    return $globalcharges->countryport_dest;
                }
            })
            ->editColumn('typedestinylb', function ($globalcharges) {
                return $globalcharges->charge_type;
            })
            ->editColumn('calculationtypelb', function ($globalcharges) {
                return $globalcharges->calculation_type;
            })
            ->editColumn('currencylb', function ($globalcharges) {
                return $globalcharges->currency_code;
            })
            ->editColumn('carrierlb', function ($globalcharges) {
                return $globalcharges->carrier;
            })
            ->editColumn('validitylb', function ($globalcharges) {
                return $globalcharges->valid_from . '/' . $globalcharges->valid_until;
            })
            ->addColumn('action', function ($globalcharges) {
                return '<a  id="edit_l" onclick="AbrirModal(' . "'editGlobalCharge'" . ',' . $globalcharges->id . ')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <!--<a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l' . $globalcharges->id . '" class="la la-times-circle"></i>
										</a>-->

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal(' . "'duplicateGlobalCharge'" . ',' . $globalcharges->id . ')">
											<i class="la la-plus"></i>
				   </a>';
            })
            //->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            //->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function createAdm_proc(Request $request)
    {
        /*  $globalcharges = ViewGlobalCharge::select(['id','charge','charge_type','calculation_type','origin_port','origin_country','destination_port','destination_country','carrier','amount','currency_code','valid_from','valid_until','company_user'])->companyUser($request->company_id)->carrier($request->carrier);*/

        $co = 0;
        $ca = 0;

        if ($request->company_id) {
            $co = $request->company_id;
        }

        if ($request->carrier) {
            $ca = $request->carrier;
        }

        $data1 = \DB::select(\DB::raw('call select_globalcharge_adm(' . $co . ',' . $ca . ')'));
        $globalcharges = new Collection;
        for ($i = 0; $i < count($data1); $i++) {
            $globalcharges->push([
                'id' => $data1[$i]->id,
                'charge' => $data1[$i]->charge,
                'charge_type' => $data1[$i]->charge_type,
                'calculation_type' => $data1[$i]->calculation_type,
                'origin_port' => $data1[$i]->origin_port,
                'origin_country' => $data1[$i]->origin_country,
                'destination_port' => $data1[$i]->destination_port,
                'destination_country' => $data1[$i]->destination_country,

                'portcountry_orig' => $data1[$i]->portcountry_orig,
                'portcountry_dest' => $data1[$i]->portcountry_dest,

                'countryport_orig' => $data1[$i]->countryport_orig,
                'countryport_dest' => $data1[$i]->countryport_dest,

                'carrier' => $data1[$i]->carrier,
                'amount' => $data1[$i]->amount,
                'currency_code' => $data1[$i]->currency_code,
                'valid_from' => $data1[$i]->valid_from,
                'valid_until' => $data1[$i]->valid_until,
                'company_user' => $data1[$i]->company_user,

            ]);
        }

        return DataTables::of($globalcharges)
            ->addColumn('surchargelb', function ($globalcharges) {
                return $globalcharges['charge'];
            })
            ->addColumn('origin_portLb', function ($globalcharges) {
                if (empty($globalcharges['origin_port']) != true) {
                    return $globalcharges['origin_port'];
                } else if (empty($globalcharges['origin_country']) != true) {
                    return $globalcharges['origin_country'];
                } else if (empty($globalcharges['portcountry_orig']) != true) {
                    return $globalcharges['portcountry_orig'];
                } else if (empty($globalcharges['countryport_orig']) != true) {
                    return $globalcharges['countryport_orig'];
                }
            })
            ->addColumn('destiny_portLb', function ($globalcharges) {
                if (empty($globalcharges['destination_port']) != true) {
                    return $globalcharges['destination_port'];
                } else if (empty($globalcharges['destination_country']) != true) {
                    return $globalcharges['destination_country'];
                } else if (empty($globalcharges['portcountry_dest']) != true) {
                    return $globalcharges['portcountry_dest'];
                } else if (empty($globalcharges['countryport_dest']) != true) {
                    return $globalcharges['countryport_dest'];
                }
            })
            ->addColumn('typedestinylb', function ($globalcharges) {
                return $globalcharges['charge_type'];
            })
            ->addColumn('calculationtypelb', function ($globalcharges) {
                return $globalcharges['calculation_type'];
            })
            ->addColumn('currencylb', function ($globalcharges) {
                return $globalcharges['currency_code'];
            })
            ->addColumn('carrierlb', function ($globalcharges) {
                return $globalcharges['carrier'];
            })
            ->addColumn('validitylb', function ($globalcharges) {
                return $globalcharges['valid_from'] . '/' . $globalcharges['valid_until'];
            })
            ->addColumn('action', function ($globalcharges) {
                return '<a  id="edit_l" onclick="AbrirModal(' . "'editGlobalCharge'" . ',' . $globalcharges['id'] . ')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <!--<a  id="remove_l{{$loop->index}}"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l' . $globalcharges['id'] . '" class="la la-times-circle"></i>
										</a>-->

                    <a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="AbrirModal(' . "'duplicateGlobalCharge'" . ',' . $globalcharges['id'] . ')">
											<i class="la la-plus"></i>
				   </a>';
            })
            //->addColumn('checkbox', '<input type="checkbox" name="check[]" class="checkbox_global" value="{{$id}}" />')
            //->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function addAdm(Request $request)
    {
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');
        $countries = Country::pluck('name', 'id');
        $calculationT = CalculationType::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $company_usersO = CompanyUser::all();
        $currency_cfg = Currency::pluck('name', 'id');
        $company_users = ['null' => 'Please Select'];
        foreach ($company_usersO as $d) {
            $company_users[$d['id']] = $d->name;
        }
        return view('globalchargesAdm.add', compact('harbor', 'carrier', 'currency', 'calculationT', 'company_users', 'typedestiny', 'countries', 'currency_cfg', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function typeChargeAdm(Request $request, $id)
    {
        if ($request->ajax()) {
            if ($id != 'null') {
                $surcharges = Surcharge::where('company_user_id', '=', $id)->get();
            } else {
                $surcharges = null;
            }
            return response()->json($surcharges);
        }
    }

    public function storeAdm(Request $request)
    {
        //dd($request);
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $detailscharges = $request->input('type');
        $calculation_type = $request->input('calculationtype');

        //$changetype = $type->find($request->input('changetype.'.$key2))->toArray();
        $detailcarrier = $request->input('localcarrier');
        foreach ($detailcarrier as $c => $carrier) {
            foreach ($calculation_type as $ct => $ctype) {
                $global = new GlobalCharge();
                $validation = explode('/', $request->validation_expire);
                $global->validity = $validation[0];
                $global->expire = $validation[1];
                $global->surcharge_id = $request->input('type');
                $global->typedestiny_id = $request->input('changetype');
                $global->calculationtype_id = $ctype;
                $global->ammount = $request->input('ammount');
                $global->currency_id = $request->input('localcurrency_id');
                $global->company_user_id = $request->company_user_id;
                $global->save();

                $detailcarrier = new GlobalCharCarrier();
                $detailcarrier->carrier_id = $carrier;
                $detailcarrier->globalcharge()->associate($global);
                $detailcarrier->save();

                $typerate = $request->input('typeroute');
                if ($typerate == 'port') {
                    $detailport = $request->input('port_orig');
                    $detailportDest = $request->input('port_dest');
                    foreach ($detailport as $p => $value) {
                        foreach ($detailportDest as $dest => $valuedest) {
                            $ports = new GlobalCharPort();
                            $ports->port_orig = $value;
                            $ports->port_dest = $valuedest;
                            $ports->typedestiny_id = $request->input('changetype');
                            $ports->globalcharge()->associate($global);
                            $ports->save();
                        }
                    }
                } elseif ($typerate == 'country') {
                    $detailCountrytOrig = $request->input('country_orig');
                    $detailCountryDest = $request->input('country_dest');
                    foreach ($detailCountrytOrig as $p => $valueC) {
                        foreach ($detailCountryDest as $dest => $valuedestC) {
                            $detailcountry = new GlobalCharCountry();
                            $detailcountry->country_orig = $valueC;
                            $detailcountry->country_dest = $valuedestC;
                            $detailcountry->globalcharge()->associate($global);
                            $detailcountry->save();
                        }
                    }
                } elseif ($typerate == 'portcountry') {
                    $detailPortCountrytOrig = $request->input('portcountry_orig');
                    $detailPortCountryDest = $request->input('portcountry_dest');
                    foreach ($detailPortCountrytOrig as $p => $valuePCorig) {
                        foreach ($detailPortCountryDest as $dest => $valuePCdest) {
                            $detail = new GlobalCharPortCountry();
                            $detail->port_orig = $valuePCorig;
                            $detail->country_dest = $valuePCdest;
                            $detail->globalcharge()->associate($global);
                            $detail->save();
                        }
                    }
                } elseif ($typerate == 'countryport') {
                    $detailCountryPortOrig = $request->input('countryport_orig');
                    $detailCountryPortDest = $request->input('countryport_dest');
                    foreach ($detailCountryPortOrig as $p => $valueCPorig) {
                        foreach ($detailCountryPortDest as $dest => $valueCPdest) {
                            $detail = new GlobalCharCountryPort();
                            $detail->country_orig = $valueCPorig;
                            $detail->port_dest = $valueCPdest;
                            $detail->globalcharge()->associate($global);
                            $detail->save();
                        }
                    }
                }
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->route('gcadm.index', compact('company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function showAdm(Request $request, $id)
    {

        $objsurcharge = new Surcharge();
        $countries = Country::pluck('name', 'id');
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $globalcharges = GlobalCharge::find($id);
        $calculationT = CalculationType::pluck('name', 'id');
        $regionCt = Region::pluck('name', 'id');
        $regionPt = RegionPt::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $surcharge = Surcharge::where('company_user_id', '=', $globalcharges->company_user_id)->pluck('name', 'id');
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $company_users = CompanyUser::pluck('name', 'id');
        $validation_expire = $globalcharges->validity . " / " . $globalcharges->expire;
        $globalcharges->setAttribute('validation_expire', $validation_expire);

        $activacion = array("rdrouteP" => false, "rdrouteC" => false, "rdroutePC" => false, "rdrouteCP" => false, 'act' => '');

        if (!$globalcharges->globalcharcountry->isEmpty()) {
            $activacion['rdrouteC'] = true;
            $activacion['act'] = 'divcountry';
        }
        if (!$globalcharges->globalcharportcountry->isEmpty()) {
            $activacion['rdroutePC'] = true;
            $activacion['act'] = 'divportcountry';
        }
        if (!$globalcharges->globalcharcountryport->isEmpty()) {
            $activacion['rdrouteCP'] = true;
            $activacion['act'] = 'divcountryport';
        }
        if (!$globalcharges->globalcharport->isEmpty()) {
            $activacion['rdrouteP'] = true;
            $activacion['act'] = 'divport';
        }

        return view('globalchargesAdm.edit', compact('globalcharges', 'harbor', 'carrier', 'regionPt', 'regionCt', 'currency', 'company_users', 'calculationT', 'typedestiny', 'surcharge', 'countries', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT', 'activacion'));
    }

    public function updateAdm(Request $request, $id)
    {
        //dd($request->all()) ;

        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $calculationT = CalculationType::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');

        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $global = GlobalCharge::find($id);
        $validation = explode('/', $request->validation_expire);
        $global->validity = $validation[0];
        $global->expire = $validation[1];
        $global->surcharge_id = $request->input('surcharge_id');
        $global->typedestiny_id = $request->input('changetype');
        $global->calculationtype_id = $request->input('calculationtype_id');
        $global->ammount = $request->input('ammount');
        $global->currency_id = $request->input('currency_id');
        $global->company_user_id = $request->input('company_user_id');

        $carrierInp = $request->input('carrier_id');
        $deleteCarrier = GlobalCharCarrier::where("globalcharge_id", $id);
        $deleteCarrier->delete();
        $deletePort = GlobalCharPort::where("globalcharge_id", $id);
        $deletePort->delete();
        $deleteCountry = GlobalCharCountry::where("globalcharge_id", $id);
        $deleteCountry->delete();
        $deletePortCountry = GlobalCharPortCountry::where("globalcharge_id", $id);
        $deletePortCountry->delete();

        $deleteCountryPort = GlobalCharCountryPort::where("globalcharge_id", $id);
        $deleteCountryPort->delete();
        $global->update();
        $contador = 1;
        foreach ($carrierInp as $key) {
            if ($contador > 1) {
                $global = null;
                $id = null;
                $global = new GlobalCharge();
                $global->validity = $validation[0];
                $global->expire = $validation[1];
                $global->surcharge_id = $request->input('surcharge_id');
                $global->typedestiny_id = $request->input('changetype');
                $global->calculationtype_id = $request->input('calculationtype_id');
                $global->ammount = $request->input('ammount');
                $global->currency_id = $request->input('currency_id');
                $global->company_user_id = $request->input('company_user_id');
                $global->save();
                $id = $global->id;
            }
            $typerate = $request->input('typeroute');
            if ($typerate == 'port') {
                $port_orig = $request->input('port_orig');
                $port_dest = $request->input('port_dest');
                foreach ($port_orig as $orig => $valueorig) {
                    foreach ($port_dest as $dest => $valuedest) {
                        $detailport = new GlobalCharPort();
                        $detailport->port_orig = $valueorig;
                        $detailport->port_dest = $valuedest;
                        $detailport->typedestiny_id = $request->input('changetype');
                        $detailport->globalcharge_id = $id;
                        $detailport->save();
                    }
                }
            } elseif ($typerate == 'country') {

                $detailCountrytOrig = $request->input('country_orig');
                $detailCountryDest = $request->input('country_dest');
                foreach ($detailCountrytOrig as $p => $valueC) {
                    foreach ($detailCountryDest as $dest => $valuedestC) {
                        $detailcountry = new GlobalCharCountry();
                        $detailcountry->country_orig = $valueC;
                        $detailcountry->country_dest = $valuedestC;
                        $detailcountry->globalcharge()->associate($global);
                        $detailcountry->save();
                    }
                }
            } elseif ($typerate == 'portcountry') {
                $detailPortCountrytOrig = $request->input('portcountry_orig');
                $detailPortCountryDest = $request->input('portcountry_dest');
                foreach ($detailPortCountrytOrig as $p => $valuePCorig) {
                    foreach ($detailPortCountryDest as $dest => $valuePCdest) {
                        $detail = new GlobalCharPortCountry();
                        $detail->port_orig = $valuePCorig;
                        $detail->country_dest = $valuePCdest;
                        $detail->globalcharge()->associate($global);
                        $detail->save();
                    }
                }
            } elseif ($typerate == 'countryport') {
                $detailCountryPortOrig = $request->input('countryport_orig');
                $detailCountryPortDest = $request->input('countryport_dest');
                foreach ($detailCountryPortOrig as $p => $valueCPorig) {
                    foreach ($detailCountryPortDest as $dest => $valueCPdest) {
                        $detail = new GlobalCharCountryPort();
                        $detail->country_orig = $valueCPorig;
                        $detail->port_dest = $valueCPdest;
                        $detail->globalcharge()->associate($global);
                        $detail->save();
                    }
                }
            }

            $detailcarrier = new GlobalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->globalcharge_id = $id;
            $detailcarrier->save();
            $contador = $contador + 1;
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully updated this contract.');
        return redirect()->route('gcadm.index', compact('company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function dupicateAdm(Request $request, $id)
    {

        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');
        $globalcharges = GlobalCharge::find($id);
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $surcharge = Surcharge::where('company_user_id', '=', $globalcharges->company_user_id)->pluck('name', 'id');
        $countries = Country::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $calculationT = CalculationType::pluck('name', 'id');
        $company_users = CompanyUser::pluck('name', 'id');
        $validation_expire = $globalcharges->validity . " / " . $globalcharges->expire;
        $globalcharges->setAttribute('validation_expire', $validation_expire);

        $activacion = array("rdrouteP" => false, "rdrouteC" => false, "rdroutePC" => false, "rdrouteCP" => false, 'act' => '');

        if (!$globalcharges->globalcharcountry->isEmpty()) {
            $activacion['rdrouteC'] = true;
            $activacion['act'] = 'divcountry';
        }
        if (!$globalcharges->globalcharportcountry->isEmpty()) {
            $activacion['rdroutePC'] = true;
            $activacion['act'] = 'divportcountry';
        }
        if (!$globalcharges->globalcharcountryport->isEmpty()) {
            $activacion['rdrouteCP'] = true;
            $activacion['act'] = 'divcountryport';
        }
        if (!$globalcharges->globalcharport->isEmpty()) {
            $activacion['rdrouteP'] = true;
            $activacion['act'] = 'divport';
        }

        return view('globalchargesAdm.duplicate', compact('globalcharges', 'harbor', 'carrier', 'company_users', 'currency', 'calculationT', 'typedestiny', 'surcharge', 'countries', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT', 'activacion'));
    }

    public function dupicateArrAdm(Request $request)
    {
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $company_users = CompanyUser::pluck('name', 'id');
        $globals_id_array = $request->input('id');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        //dd($globals_id_array);
        return view('globalchargesAdm.duplicateArray', compact('count', 'company_users', 'globals_id_array', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function storeArrayAdm(Request $request)
    {
        //dd($request->all());
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');
        $requestJob = $request->all();
        if (env('APP_VIEW') == 'operaciones') {
            GCDplFclLcl::dispatch($requestJob, 'fcl')->onQueue('operaciones');
        } else {
            GCDplFclLcl::dispatch($requestJob, 'fcl');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');
        return redirect()->route('gcadm.index', compact('company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function editDateArrAdm(Request $request)
    {
        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $company_users = CompanyUser::pluck('name', 'id');
        $globals_id_array = $request->input('idAr');
        $count = count($globals_id_array);
        //$global             = $global->toArray();
        return view('globalchargesAdm.EditDatesArray', compact('count', 'company_users', 'globals_id_array', 'company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }

    public function updateDateArrAdm(Request $request)
    {
        //dd($request->all());
        $date = explode('/', $request->validation_expire);
        $date_start = trim($date[0]);
        $date_end = trim($date[1]);
        foreach ($request->idArray as $global) {
            $globalObj = null;
            $globalObj = GlobalCharge::find($global);
            $globalObj->validity = $date_start;
            $globalObj->expire = $date_end;
            $globalObj->update();
        }

        $company_user_id_selec = $request->input('company_user_id_selec');
        $carrier_id_selec = $request->input('carrier_id_selec');
        $reload_DT = $request->input('reload_DT');

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully updated.');
        return redirect()->route('gcadm.index', compact('company_user_id_selec', 'carrier_id_selec', 'reload_DT'));
    }
}
