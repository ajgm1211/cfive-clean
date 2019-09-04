<?php

namespace App\Http\Controllers;

use Excel;
use PrvHarbor;
use App\Region;
use App\Harbor;
use PrvCarrier;
use App\Country;
use App\Carrier;
use App\Currency;
use App\Surcharge;
use Carbon\Carbon;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\CalculationTypeLcl;
use Illuminate\Http\Request;
use App\GlobalCharCarrierLcl;
use App\GlobalCharCountryLcl;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\Storage;
use App\AccountImportationGlobalChargerLcl;
use App\NewRequestGlobalChargerLcl as RequestGCLCL;

class ImportationGlobalChargerLclController extends Controller
{

    public function index(){
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.index',compact('harbor','region','country','carrier','companysUser','typedestiny'));
    }

    // precarga la vista para importar globals
    public function indexRequest($id){
        $requestgc      = RequestGCLCL::find($id);
        //dd($requestgc);
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.indexRequest',compact('harbor','country','region','carrier','companysUser','typedestiny','requestgc'));
    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request){
        //dd($request->all());
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $request_id         = $request->request_id;
        $carrierVal         = $request->carrier;
        $typedestinyVal     = $request->typedestiny;
        $validitydateVal    = $request->validitydate;
        $destinyArr         = $request->destiny;
        $originArr          = $request->origin;
        $originCountArr     = $request->originCount;
        $originRegionArr    = $request->originRegion;
        $destinyCountArr    = $request->destinyCount;
        $destinyRegionArr   = $request->destinyRegion;
        $CompanyUserId      = $request->CompanyUserId;
        $statustypecurren   = $request->valuesCurrency;
        $statusPortCountry  = $request->valuesportcountry;

        $carrierBol         = false;
        $destinyBol         = false;
        $originBol          = false;
        $typedestinyBol     = false;
        $datevalidityBol    = false;
        $filebool           = false;

        $data           = collect([]);
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        $harbor         = harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $carrier        = carrier::all()->pluck('name','id');


        $file           = $request->file('file');
        $ext            = strtolower($file->getClientOriginalExtension());
        $validator      = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );
        $Contract_id;
        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('ImportationGlobalChargerLcl.index');
        }
        //obtenemos el nombre del archivo
        $nombre     = $file->getClientOriginalName();
        $nombre     = $now.'_'.$nombre;
        $filebool   = \Storage::disk('GCImportLcl')->put($nombre,\File::get($file));

        if($filebool){
            Storage::disk('GCAccountLcl')->put($nombre,\File::get($file));
            $account                   = new AccountImportationGlobalChargerLcl();
            $account->name             = $request->name;
            $account->date             = $request->date;
            $account->namefile         = $nombre;
            $account->company_user_id  = $CompanyUserId;
            $account->requestgclcl_id     = $request_id;
            $account->status           = 'incomplete';
            $account->save(); 

            ProcessContractFile::dispatch($account->id,$account->namefile,'gclcl','account');

            $account_id = $account->id;
            /*$fileTmp    = new FileTmpGlobalcharge();
            $fileTmp->account_id = $account_id;
            $fileTmp->name_file   = $nombre;
            $fileTmp->save(); //*/
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error storage:link!!');
            return redirect()->route('ImportationGlobalchargeFcl.index');
        }


        $targetsArr =[ 
            0 => "Calculation Type",
            1 => "Charge",
            2 => "Amount",
            3 => "Minimun"
        ];

        // DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox

        /* si $statusPortCountry es igual a 2, se agrega una columna que diferencia puertos de paises
        , si es 1 el solo se mapean puertos        
        */
        if($statusPortCountry == 2){
            array_push($targetsArr,"Differentiator");
        }

        /* si $statustypecurren es igual a 2, los currencys estan contenidos en la misma columna 
        con los valores, si es uno el currency viene en una colmna aparte        
        */

        if($statustypecurren == 1){
            array_push($targetsArr,"Currency");
        }

        // ------- ORIGIN -------------------------
        if($request->DatOri == false){
            array_push($targetsArr,'Origin');
        }
        else{
            $originBol = true;
            $originArr;
        }

        // ------- DESTINY ------------------------
        if($request->DatDes == false){
            array_push($targetsArr,'Destiny');
        } else {
            $destinyArr;
            $destinyBol = true;
        }

        // ------- CARRIER ------------------------
        if($request->DatCar == false){
            array_push($targetsArr,'Carrier');
        } else {
            $carrierVal;
            $carrierBol = true;
        }

        // ------- TYPE DESTINY -------------------
        if($request->DatTypeDes == false){
            array_push($targetsArr,'Type Destiny');
        } else {
            $typedestinyVal;
            $typedestinyBol = true;
        }

        // ------- DATE VAIDITY -------------------
        if($request->DatDtValid == false){
            array_push($targetsArr,'Validity From');
            array_push($targetsArr,'Validity To');
        } else {
            $validitydateVal;
            $datevalidityBol = true;
        }

        $coordenates = collect([]);

        //ini_set('memory_limit', '1024M');

        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('GCImportLcl')
                   ->url($nombre),function($reader) use($request,$coordenates) {
                       $reader->takeRows(2);
                       $reader->noHeading = true;
                       $reader->ignoreEmpty();

                       $read = $reader->first();
                       $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
                       for($i=0;$i<count($reader->first());$i++){
                           $coordenates->push($columna[$i].' '.$read[$i]);
                       }
                   });

        $boxdinamy = [
            'existorigin'       => $originBol,
            'origin'            => $originArr,
            'existdestiny'      => $destinyBol,
            'destiny'           => $destinyArr,
            'originCount'       => $originCountArr,
            'originRegion'      => $originRegionArr,
            'destinyCount'      => $destinyCountArr,
            'destinyRegion'     => $destinyRegionArr,
            'existcarrier'      => $carrierBol,
            'carrier'           => $carrierVal,            
            'existtypedestiny'  => $typedestinyBol,
            'typedestiny'       => $typedestinyVal,
            'existdatevalidity' => $datevalidityBol,
            'validitydate'      => $validitydateVal,
            'account_id'        => $account_id,
            'date'              => $request->date,
            'name'              => $request->name,
            'fileName'          => $nombre,

        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('importationGlobalChargerLcl.show',compact('harbor',
                                                               'region',
                                                               'country',
                                                               'data',
                                                               'carrier',
                                                               'targetsArr',
                                                               'account_id',
                                                               'account',
                                                               'coordenates',
                                                               'countTarges',
                                                               'CompanyUserId',
                                                               'statustypecurren',
                                                               'statusPortCountry',
                                                               'typedestiny'));
    }

    public function create(Request $request)
    {
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        //dd($request->all());

        $requestobj = $request;
        $companyUserIdVal = $companyUserId;
        $errors = 0;
        $NameFile = $requestobj['FileName'];
        $path = \Storage::disk('GCImportLcl')->url($NameFile);

        Excel::selectSheetsByIndex(0)
            ->Load($path,function($reader) use($requestobj,$errors,$NameFile,$companyUserIdVal) {
                $reader->noHeading = true;

                $minimun                = "Minimun";
                $amount                 = "Amount";                
                $origin                 = "origin";
                $originExc              = "Origin";
                $destiny                = "destiny";
                $destinyExc             = "Destiny";
                $currency               = "Currency";
                $originCountry          = "originCount";//arreglo de multiples country
                $originRegion           = "originRegion";//arreglo de multiples Region
                $destinycountry         = "destinyCount";//arreglo de multiples country
                $destinyRegion          = "destinyRegion";//arreglo de multiples Region
                $carrier                = "Carrier";
                $CalculationType        = "Calculation_Type";
                $Charge                 = "Charge";
                $statustypecurren       = "statustypecurren";
                $typedestiny            = "Type_Destiny";
                $validityfrom           = "Validity_From";
                $validityto             = "Validity_To";
                $differentiator         = "Differentiator";

                $statusPortCountryTW        = $requestobj['statusPortCountry'];
                $account_id                 = $requestobj['account_id'];
                $statusexistfortynor        = $requestobj['existfortynor'];
                $statusexistfortyfive       = $requestobj['existfortyfive'];
                $statusexistdatevalidity    = $requestobj['existdatevalidity'];
                $statusPortCountry          = $requestobj['statusPortCountry'];

                $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

                $globalChargerCollection    = collect([]);
                $globalChargerFailCollection   = collect([]);


                $i = 1;
                $falli =0;
                foreach($reader->get() as $read){

                    //--------------------------------------------------------
                    if($i != 1){
                        $differentiatorVal = '';
                        if($statusPortCountryTW == 2){
                            $differentiatorVal = $read[$requestobj[$differentiator]];
                        } else {
                            $differentiatorVal = 'port';
                        }
                        //--------------- CARGADOR DE ARREGLO ORIGEN DESTINO MULTIPLES ----------------------------
                        //--- ORIGIN ------------------------------------------------------
                        $oricount = 0;
                        if($requestobj['existorigin'] == true){
                            $originMultps = [0];
                        } else {
                            $originMultps = explode('|',$read[$requestobj[$originExc]]);
                            foreach($originMultps as $originMultCompact){
                                if(strnatcasecmp($differentiatorVal,'region') == 0){
                                    $originMultCompact = trim($originMultCompact);
                                    $regionsOR = Region::where('name','like','%'.$originMultCompact.'%')->with('CountriesRegions.country')->get();
                                    if(count($regionsOR) == 1){
                                        // region add
                                        foreach($regionsOR as $regionor){   
                                            if($oricount == 0){
                                                $originMultps = $regionor->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                            } else {
                                                foreach($regionor->CountriesRegions->pluck('country')->pluck('name')->toArray() as $oricountriesarray){
                                                    array_push($originMultps,$oricountriesarray);
                                                }
                                            }
                                        }
                                    } elseif(count($regionsOR) == 0) {
                                        // pais add
                                        if($oricount == 0){
                                            $originMultps =[$originMultCompact];
                                        } else {
                                            array_push($originMultps,$originMultCompact);
                                        }
                                    }
                                }
                                $oricount++;
                            }
                        }
                        //--- DESTINY -----------------------------------------------------
                        $descount = 0;
                        if($requestobj['existdestiny'] == true){
                            $destinyMultps = [0];
                        } else {
                            $destinyMultps = explode('|',$read[$requestobj[$destinyExc]]);
                            foreach($destinyMultps as $destinyMultCompact){
                                if(strnatcasecmp($differentiatorVal,'region') == 0){
                                    $destinyMultCompact = trim($destinyMultCompact);
                                    $regionsDES = Region::where('name','like','%'.$destinyMultCompact.'%')->with('CountriesRegions.country')->get();
                                    if(count($regionsDES) == 1){
                                        // region add
                                        foreach($regionsDES as $regiondes){                                            
                                            if($descount == 0){
                                                $destinyMultps = $regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                            } else {
                                                foreach($regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray() as $descountriesarray){
                                                    array_push($destinyMultps,$descountriesarray);
                                                }
                                            }
                                        }
                                    } elseif(count($regionsDES) == 0) {
                                        // pais add
                                        if($descount == 0){
                                            $destinyMultps =[$destinyMultCompact];
                                        } else {
                                            array_push($destinyMultps,$destinyMultCompact);
                                        }

                                    }
                                }
                                $descount++;
                            }
                        }

                        //dd($originMultps);
                        //dd($destinyMultps);

                        foreach($originMultps as $originMult){
                            foreach($destinyMultps as $destinyMult){

                                $carrierVal                 = '';
                                $typedestinyVal             = '';
                                $originVal                  = '';
                                $destinyVal                 = '';
                                $origenFL                   = '';
                                $destinyFL                  = '';
                                $currencyVal                = '';
                                $currencyReadVal            = '';
                                $currencyReadVal            = '';
                                $minimunVal                 = null;
                                $amountVal                  = null;
                                

                                $currencyValAmount          = '';
                                $currencyValMinimun         = '';

                                $calculationtypeVal         = '';
                                $surchargelist              = '';
                                $surchargeVal               = '';
                                $validityfromVal            = '';
                                $validitytoVal		        = '';
                                $differentiatorVal          = 1;
                                $account_idVal              = $account_id;

                                $calculationtypeValfail     = '';
                                $currencResultwen           = '';
                                $currencResulfor            = '';

                                $currencResul               = '';

                                $minimunArr                 = [];
                                $amountArr                  = [];


                                $originBol               = false;
                                $origExiBol              = false;
                                $destinyBol              = false;
                                $destiExitBol            = false;
                                $typedestinyExitBol      = false;
                                $typedestinyBol          = false;
                                $carriExitBol            = false;
                                $curreExiBol             = false;
                                $curreExitBol            = false;
                                $curreExiMinimunBol      = false;
                                $curreExiAmountBol       = false;

                                $minimunExiBol           = false;
                                $amountExiBol            = false;

                                $carriBol                = false;
                                $calculationtypeExiBol   = false;
                                $variantecurrency        = false;
                                $typeExiBol              = false;
                                $minimunArrBol           = false;
                                $amountArrBol            = false;
                                $validityfromExiBol		 = false;
                                $validitytoExiBol		 = false;
                                $differentiatorBol       = false;
                                $values                  = true;

                                if($requestobj[$statustypecurren] == 1){
                                    $currencyReadVal        = $read[$requestobj[$currency]];
                                } 
                                
                                if($requestobj['existorigin'] != 1){
                                    $differentiatorValTw    = null;
                                }
                                
                                if($requestobj['existorigin'] != 1 && $requestobj['existdestiny'] != 1){
                                    $randons    = [];
                                }

                                $minimunVal                 = $read[$requestobj[$minimun]];
                                $amountVal                  = $read[$requestobj[$amount]];
                                $calculationvalvaration     = $read[$requestobj[$CalculationType]];
                                $chargerValRead             = $read[$requestobj[$Charge]];


                                if($statusexistdatevalidity == 1){
                                    $dateArr = explode('/',$requestobj['validitydate']);
                                    $validityfromVal    = trim($dateArr[0]);
                                    $validitytoVal      = trim($dateArr[1]);
                                } else{
                                    $validityfromVal = $read[$requestobj[$validityfrom]];
                                    $validitytoVal = $read[$requestobj[$validityto]];
                                }

                                //--------------- DIFRENCIADOR HARBOR COUNTRY -----------------------------------------

                                if($statusPortCountry == 2){
                                    $differentiatorVal = $read[$requestobj[$differentiator]];// hacer validacion de puerto o country
                                    $differentiatorValTw = $read[$requestobj[$differentiator]];// hacer validacion de puerto o country
                                    if(strnatcasecmp($differentiatorVal,'country') == 0 || strnatcasecmp($differentiatorVal,'region') == 0){
                                        $differentiatorBol = true;
                                        $differentiatorVal = 2;
                                    } else {
                                        $differentiatorVal = 1;
                                    }
                                }

                                //--------------- ORIGEN MULTIPLE O SIMPLE --------------------------------------------

                                if($requestobj['existorigin'] == 1){
                                    $originBol = true;
                                    $origExiBol = true; //segundo boolean para verificar campos errados
                                    if($differentiatorBol == false){
                                        $randons = $requestobj[$origin];
                                    } else if($differentiatorBol == true){
                                        if(strnatcasecmp($differentiatorValTw,'country') == 0){
                                            $randons = $requestobj[$originCountry];
                                        } else{
                                            $randons = [];
                                            foreach($requestobj[$originRegion] as $randosoriR){
                                                $regionsORIrans = Region::with('CountriesRegions.country')->find($randosoriR);
                                                foreach($regionsORIrans->CountriesRegions->pluck('country')->pluck('id')->toArray() as $regionsORIran){
                                                    array_push($randons,$regionsORIran);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //$originVal = $read[$requestobj[$originExc]];// hacer validacion de puerto en DB
                                    $originVal = trim($originMult);// hacer validacion de puerto en DB
                                    if($differentiatorBol == false){
                                        // El origen es  por puerto
                                        $resultadoPortOri = PrvHarbor::get_harbor($originVal);
                                        if($resultadoPortOri['boolean']){
                                            $origExiBol = true;    
                                        }
                                        $originVal  = $resultadoPortOri['puerto'];
                                    } else if($differentiatorBol == true){
                                        // El origen es  por country
                                        $resultadocountrytOri = PrvHarbor::get_country($originVal);
                                        if($resultadocountrytOri['boolean']){
                                            $origExiBol = true;    
                                        }
                                        $originVal  = $resultadocountrytOri['country'];
                                    }

                                }
                                //dd($originVal);
                                //---------------- DESTINO MULTIPLE O SIMPLE ------------------------------------------

                                if($requestobj['existdestiny'] == 1){
                                    $destinyBol = true;
                                    $destiExitBol = true; //segundo boolean para verificar campos errados
                                    if($differentiatorBol == false){
                                        $randons = $requestobj[$destiny];
                                    } else if($differentiatorBol == true){
                                        if(strnatcasecmp($differentiatorValTw,'country') == 0){
                                            $randons = $requestobj[$destinycountry];
                                        } else{
                                            $randons = $requestobj[$destinyRegion];

                                            $randons = [];
                                            foreach($requestobj[$destinyRegion] as $randosdesR){
                                                $regionsDEsrans = Region::with('CountriesRegions.country')->find($randosdesR);
                                                foreach($regionsDEsrans->CountriesRegions->pluck('country')->pluck('id')->toArray() as $regionsDESran){
                                                    array_push($randons,$regionsDESran);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //$destinyVal = $read[$requestobj[$destinyExc]];// hacer validacion de puerto en DB
                                    $destinyVal = trim($destinyMult);// hacer validacion de puerto en DB
                                    if($differentiatorBol == false){
                                        // El origen es  por Harbors
                                        $resultadoPortDes = PrvHarbor::get_harbor($destinyVal);
                                        if($resultadoPortDes['boolean']){
                                            $destiExitBol = true;    
                                        }
                                        $destinyVal  = $resultadoPortDes['puerto'];
                                    } else if($differentiatorBol == true){
                                        //El destino es por Country
                                        $resultadocountryDes = PrvHarbor::get_country($destinyVal);
                                        if($resultadocountryDes['boolean']){
                                            $destiExitBol = true;    
                                        }
                                        $destinyVal  = $resultadocountryDes['country'];
                                    }
                                }

                                //dd($destinyVal);
                                //dd($randons);
                                //------------------ VALIDITY FROM ----------------------------------------------------

                                try{
                                    $validityfromVal = Carbon::parse($validityfromVal)->format('Y-m-d');
                                    $validityfromExiBol = true;
                                } catch (\Exception $err){
                                    $validityfromVal = $validityfromVal.'_E_E';
                                }

                                //------------------ VALIDITY TO ------------------------------------------------------

                                try{
                                    $validitytoVal = Carbon::parse($validitytoVal)->format('Y-m-d');
                                    $validitytoExiBol = true;
                                } catch (\Exception $err){
                                    $validitytoVal = $validitytoVal.'_E_E';
                                }

                                //--------------- Type Destiny --------------------------------------------------------

                                if($requestobj['existtypedestiny'] == 1){
                                    $typedestinyExitBol = true;
                                    $typedestinyBol     = true;
                                    $typedestinyVal     = $requestobj['typedestiny']; // es cuando se indica que no posee type destiny 
                                } else {
                                    $typedestinyVal      = $read[$requestobj[$typedestiny]]; // cuando el type destiny  existe en el excel
                                    $typedestinyResul    = str_replace($caracteres,'',$typedestinyVal);
                                    $typedestinyobj      = TypeDestiny::where('description','=',$typedestinyResul)->first();
                                    if(empty($typedestinyobj->id) != true){
                                        $typedestinyExitBol = true;
                                        $typedestinyVal = $typedestinyobj->id;
                                    }else{
                                        $typedestinyVal = $typedestinyVal.'_E_E';
                                    }
                                }

                                //--------------- CARRIER -------------------------------------------------------------

                                if($requestobj['existcarrier'] == 1){
                                    $carriExitBol = true;
                                    $carriBol     = true;
                                    $carrierVal = $requestobj['carrier']; // cuando se indica que no posee carrier 
                                } else {
                                    $carrierVal = $read[$requestobj['Carrier']]; // cuando el carrier existe en el excel
                                    $carrierArr      = PrvCarrier::get_carrier($carrierVal);
                                    dd($carriersExists = Carrier::where('varation->type','like','%'.strtolower($carrierVal).'%')->get());
                                    $carriExitBol    = $carrierArr['boolean'];
                                    $carrierVal      = $carrierArr['carrier'];
                                }

                                //---------------- CURRENCY VALUES ----------------------------------------------------

                                if(empty($minimunVal) != true){ //Primero valido si el campo viene lleno, en caso contrario lo lleno manuelamene
                                    $minimunArrBol  = true;
                                    $minimunArr     = explode(' ',trim($minimunVal));
                                } else {
                                    $minimunArr     = ['0.0']; 
                                }

                                if(empty($amountVal) != true){
                                    $amountArrBol   = true;
                                    $amountArr      = explode(' ',trim($amountVal));
                                } else {
                                    $amountArr      = ['0.0'];
                                }

                                // ----------------------- Validacion de comapos vacios--------------------------------

                                if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency para no llenar el valor del currency arreglo[posicion 2] 
                                    // ------- AMOUNT'
                                    if($amountArrBol == false){ // Cargamos el arreglo[1] para que se pueda registrar
                                        if($minimunArrBol == true){
                                            array_push($amountArr,$minimunArr[1]);
                                        } else {
                                            array_push($amountArr,'');
                                        }
                                    }

                                    // ------- MINIUMUN -----------------------------------------------------------Min
                                    if($minimunArrBol == false){ // Cargamos el arreglo[1] para que el Rate se pueda registrar, y para que se validen los PER_DOC
                                        if($amountArrBol == true){
                                            array_push($minimunArr,$amountArr[1]);
                                        } else {
                                            array_push($minimunArr,'');
                                        }
                                    }
                                }

                                //---------------- AMOUNT -------------------------------------------------------------

                                if(empty($amountArr[0]) != true || floatval($amountArr[0]) == 0.00){
                                    $amountExiBol = true;
                                    $amountVal  = floatval($amountArr[0]);
                                }  else{
                                    $amountVal  = $amountArr[0].'_E_E';
                                }

                                //----------------- MINIMUN -----------------------------------------------------------

                                if(empty($minimunArr[0]) != true || floatval($minimunArr[0]) == 0.00){
                                    $minimunExiBol = true;
                                    $minimunVal   = floatval($minimunArr[0]);
                                }  else{
                                    $minimunVal = $minimunArr[0].'_E_E';
                                }                         

                                if($amountVal == 0.00
                                   && $minimunVal == 0.00){
                                    $values = false;
                                }

                                //---------------- CURRENCY -----------------------------------------------------------

                                if($requestobj[$statustypecurren] == 2){ // se verifica si el valor viene junto con el currency

                                    // cargar  columna con el  valor y currency  juntos, se descompone

                                    //---------------- CURRENCY AMUONT + VALUE ----------------------------------------

                                    if(count($amountArr) > 1){
                                        $currencyValAmount = str_replace($caracteres,'',$amountArr[1]);
                                    } else {
                                        $currencyValAmount = '';
                                    }

                                    $currencAmount = Currency::where('alphacode','=',$currencyValAmount)->first();

                                    if(empty($currencAmount->id) != true){
                                        $curreExiAmountBol = true;
                                        $currencyValAmount =  $currencAmount->id;
                                    } else{
                                        if(count($amountArr) > 1){
                                            $currencyValAmount = $amountArr[1].'_E_E';
                                        } else{
                                            $currencyValAmount = '_E_E';
                                        }
                                    }

                                    //---------------- CURRENCY MINUMUN + VALUE ---------------------------------------

                                    if(count($minimunArr) > 1){
                                        $currencResulMin = str_replace($caracteres,'',$minimunArr[1]);
                                    } else{
                                        $currencResulMin = '';
                                    }

                                    $currencMinimun = Currency::where('alphacode','=',$currencResulMin)->first();

                                    if(empty($currencMinimun->id) != true){
                                        $curreExiMinimunBol = true;
                                        $currencyValMinimun =  $currencMinimun->id;
                                    } else{
                                        if(count($minimunArr) > 1){
                                            $currencyValMinimun = $minimunArr[1].'_E_E';
                                        } else {
                                            $currencyValMinimun = '_E_E';
                                        }
                                    }

                                    if($curreExiAmountBol == true && $curreExiMinimunBol == true){
                                        $variantecurrency = true;
                                    }

                                } else {

                                    if(empty($currencyReadVal) != true){
                                        $currencResul= str_replace($caracteres,'',$currencyReadVal);
                                        $currenc = Currency::where('alphacode','=',$currencResul)->first();
                                        if(empty($currenc->id) != true){    
                                            $curreExitBol = true;
                                            $currencyVal =  $currenc->id;
                                        } else{
                                            $currencyVal = $currencyReadVal.'_E_E';                                    
                                        }
                                    }
                                    else{
                                        $currencyVal = $currencyReadVal.'_E_E';
                                    }

                                    if($curreExitBol == true ){
                                        $variantecurrency = true;
                                    }
                                }

                                //------------------ CALCULATION TYPE -------------------------------------------------

                                $calculationtype = CalculationTypeLcl::where('code','=',$calculationvalvaration)->first();
                                if(empty($calculationtype) != true){
                                    $calculationtypeExiBol  = true;
                                    $calculationtypeVal     = $calculationtype['id'];
                                } else{
                                    $calculationtypeVal     = $calculationvalvaration.'_E_E';
                                }

                                //------------------ TYPE -------------------------------------------------------------

                                if(empty($chargerValRead) != true){
                                    $typeExiBol = true;

                                    $surchargelist = Surcharge::where('name','=',$chargerValRead)
                                        ->where('company_user_id','=', $companyUserIdVal)
                                        ->first();
                                    if(empty($surchargelist) != true){
                                        $surchargeVal = $surchargelist['id'];
                                    } 	else{
                                        $surchargelist = Surcharge::create([
                                            'name'              => $chargerValRead,
                                            'description'       => $chargerValRead,
                                            'company_user_id'   => $companyUserIdVal
                                        ]);
                                        $surchargeVal = $surchargelist->id;
                                    }

                                } else {
                                    $surchargeVal = $chargerValRead.'_E_E';
                                }

                                //////////////////////////////////////////////////////////////////////////////////////////////////////

                                $prueba = collect([]);

                                $prueba = [
                                    '$differentiatorBol'         => $differentiatorBol,
                                    '$originBol'                 => $originBol,
                                    'origExiBol'                 => $origExiBol,
                                    '$destinyBol'                => $destinyBol,
                                    '$destiExitBol'              => $destiExitBol,
                                    '$typedestinyExitBol'        => $typedestinyExitBol,
                                    '$typedestinyBol'            => $typedestinyBol,
                                    '$carriExitBol'              => $carriExitBol,
                                    '$carriBol'                  => $carriBol,
                                    '$minimunArrBol'             => $minimunArrBol,
                                    '$minimunExiBol'             => $minimunExiBol,
                                    '$amountExiBol'              => $amountExiBol,
                                    '$amountArrBol'              => $amountArrBol,
                                    '$values'                    => $values,
                                    '$curreExiAmountBol'         => $curreExiAmountBol,
                                    '$curreExiMinimunBol'        => $curreExiMinimunBol,
                                    '$variantecurrency'          => $variantecurrency,
                                    '$calculationtypeExiBol'     => $calculationtypeExiBol,
                                    '$typeExiBol'                => $typeExiBol,
                                    '$validityfromVal'           => $validityfromVal,
                                    '$validitytoVal'             => $validitytoVal,
                                    '$differentiatorVal'         => $differentiatorVal,
                                    '$differentiatorValTw'       => $differentiatorValTw,
                                    '$originVal'                 => $originVal,
                                    '$destinyVal'                => $destinyVal,                 
                                    '$typedestinyVal'            => $typedestinyVal,
                                    '$carrierVal'                => $carrierVal,
                                    '$minimunVal'                => $minimunVal,
                                    '$amountVal'                 => $amountVal,
                                    '$currencyValAmount'         => $currencyValAmount,
                                    '$currencyValMinimun'        => $currencyValMinimun,
                                    '$currencyVal'               => $currencyVal,
                                    '$calculationtypeVal'        => $calculationtypeVal,
                                    '$surchargeVal'              => $surchargeVal,
                                    '$companyUserIdVal'          => $companyUserIdVal,
                                    '$minimunArr'                => $minimunArr,
                                    '$amountArr'                 => $amountArr,
                                    '$randons'                   => $randons,
                                    '$statusexistdatevalidity'   => $statusexistdatevalidity,
                                    '$calculationvalvaration'    => $calculationvalvaration,
                                    '$calculationtype'           => $calculationtype,
                                    '$chargerValRead'            => $chargerValRead
                                ];

                                dd($prueba);

                                if($carriExitBol            	== true
                                   && $origExiBol           	== true
                                   && $destiExitBol         	== true
                                   && $amountExiBol         	== true
                                   && $minimunExiBol          	== true
                                   && $calculationtypeExiBol 	== true
                                   && $variantecurrency     	== true
                                   && $typeExiBol           	== true
                                   && $typedestinyExitBol   	== true
                                   && $validityfromExiBol       == true
                                   && $validitytoExiBol         == true
                                   && $values 					== true ){

                                    if($differentiatorBol == false){ //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'globalcharportlcl';
                                    }else {  //si es country verificamos si exite uno creado con country
                                        $typeplace = 'globalcharcountrylcl';
                                    }

                                    // evaluamos si viene el valor con el currency juntos

                                    if($requestobj[$statustypecurren] == 2){
                                        $currencyVal = $currencyValtwen;
                                    }

                                    //globalcharport
                                    //globalcharcountry

                                    $ammount = $twentyVal;

                                    if($ammount != 0 || $ammount != 0.0){
                                        $globalChargeArreG = null;
                                        $globalChargeArreG = GlobalChargeLcl::where('surcharge_id',$surchargeVal)
                                            ->where('typedestiny_id',$typedestinyVal)
                                            ->where('company_user_id',$companyUserIdVal)
                                            ->where('calculationtypelcl_id',$calculationtypeVal)
                                            ->where('ammount',$amountVal)
                                            ->where('minimum',$minimunVal)
                                            ->where('validity',$validityfromVal)
                                            ->where('expire',$validitytoVal)
                                            ->where('currency_id',$currencyVal)
                                            ->has($typeplace)
                                            ->first();

                                        if(count($globalChargeArreG) == 0){
                                            $globalChargeArreG = GlobalChargeLcl::create([ // tabla GlobalCharge
                                                'surcharge_id'       						=> $surchargeVal,
                                                'typedestiny_id'     						=> $typedestinyVal,
                                                'account_imp_gclcl_id'                      => $account_idVal,
                                                'company_user_id'    						=> $companyUserIdVal,
                                                'calculationtype_id' 						=> $calculationtypeVal,
                                                'ammount'            						=> $amountVal,
                                                'minimum'            						=> $minimunVal,
                                                'validity' 									=> $validityfromVal,
                                                'expire'					 				=> $validitytoVal,
                                                'currency_id'        						=> $currencyVal
                                            ]);   
                                        }
                                        //---------------------------------- VALIDATE G.C. CARRIER -------------------------------------------

                                        $exitGCCPC = null;
                                        $exitGCCPC = GlobalCharCarrierLcl::where('carrier_id',$carrierVal)->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                        if(count($exitGCCPC) == 0){
                                            GlobalCharCarrier::create([ // tabla GlobalCharCarrier
                                                'carrier_id'            => $carrierVal,
                                                'globalchargelcl_id'    => $globalChargeArreG->id
                                            ]);
                                        }
                                        //----------------------------------- ORIGIN DESTINATION ---------------------------------------------

                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    $originVal = $rando;
                                                } else {
                                                    $destinyVal = $rando;
                                                }

                                                //---------------------------------- CAMBIAR POR ID -------------------------------

                                                if($differentiatorBol == false){
                                                    $exgcpt = null;
                                                    $exgcpt = GlobalCharPortLcl::where('port_orig',$originVal)->where('port_dest',$destinyVal)
                                                        ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                    if(count($exgcpt) == 0){
                                                        GlobalCharPortLcl::create([ // tabla GlobalCharPort
                                                            'port_orig'      	=> $originVal,
                                                            'port_dest'      	=> $destinyVal,
                                                            'globalchargelcl_id'   => $globalChargeArreG->id
                                                        ]);
                                                    }
                                                } else {
                                                    $exgcct = null;
                                                    $exgcct = GlobalCharCountryLcl::where('country_orig',$originVal)
                                                        ->where('country_dest',$destinyVal)
                                                        ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                    if(count($exgcct) == 0){
                                                        GlobalCharCountryLcl::create([ // tabla GlobalCharCountry harbor
                                                            'country_orig'          => $originVal,
                                                            'country_dest'          => $destinyVal,
                                                            'globalchargelcl_id'    => $globalChargeArreG->id
                                                        ]);
                                                    }
                                                }

                                                //---------------------------------------------------------------------------------

                                            } 
                                        }else {
                                            // fila por puerto, sin expecificar origen ni destino manualmente
                                            if($differentiatorBol == false){
                                                $exgcpt = null;
                                                $exgcpt = GlobalCharPortLcl::where('port_orig',$originVal)->where('port_dest',$destinyVal)
                                                    ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                if(count($exgcpt) == 0){
                                                    GlobalCharPortLcl::create([ // tabla GlobalCharPort
                                                        'port_orig'      	=> $originVal,
                                                        'port_dest'      	=> $destinyVal,
                                                        'globalchargelcl_id'   => $globalChargeArreG->id
                                                    ]);
                                                }
                                            } else {
                                                $exgcct = null;
                                                $exgcct = GlobalCharCountryLcl::where('country_orig',$originVal)
                                                    ->where('country_dest',$destinyVal)
                                                    ->where('globalchargelcl_id',$globalChargeArreG->id)->first();
                                                if(count($exgcct) == 0){
                                                    GlobalCharCountryLcl::create([ // tabla GlobalCharCountry harbor
                                                        'country_orig'      => $originVal,
                                                        'country_dest'      => $destinyVal,
                                                        'globalchargelcl_id'   => $globalChargeArreG->id
                                                    ]);
                                                }
                                            }
                                        }
                                        //echo $i;
                                        //dd($globalChargeArreG);
                                    }

                                } else {
                                    // van los fallidos

                                    //---------------------------- TYPE DESTINY  ----------------------------------------------------

                                    if($typedestinyExitBol == true){
                                        if($typedestinyBol == true){
                                            $typedestinyobj = TypeDestiny::find($typedestinyVal);
                                            $typedestinyVal = $typedestinyobj->description;
                                        } else {
                                            $typedestinyVal  = $read[$requestobj[$typedestiny]];
                                        }
                                    }

                                    //---------------------------- CARRIER  ---------------------------------------------------------

                                    if($carriExitBol == true){
                                        if($carriBol == true){
                                            $carrier = Carrier::find($requestobj['carrier']); 
                                            $carrierVal = $carrier['name'];  
                                        }else{
                                            $carriExitBol2   = false;
                                            $carrierArr      = PrvCarrier::get_carrier($read[$requestobj['Carrier']]);
                                            $carrierVal      = $carrierArr['carrier'];
                                            $carriExitBol2   = $carrierArr['boolean'];
                                            if($carriExitBol2 == true){
                                                $carrierVal = Carrier::find($carrierVal);
                                                $carrierVal = $carrierVal->name;
                                            }
                                        }
                                    }

                                    //---------------------------- VALUES CURRENCY ---------------------------------------------------

                                    if($curreExiBol == true){
                                        $currencyVal = $read[$requestobj[$currency]];
                                    }

                                    if( $twentyExiBol == true){
                                        if(empty($read[$requestobj[$twenty]]) == true){
                                            $twentyVal = '0';
                                        } else{
                                            $twentyVal = $read[$requestobj[$twenty]];

                                        }
                                    }

                                    if( $fortyExiBol == true){
                                        if(empty($read[$requestobj[$forty]]) == true){
                                            $fortyVal = '0';
                                        } else{
                                            $fortyVal = $read[$requestobj[$forty]];
                                        }
                                    }

                                    if( $fortyhcExiBol == true){
                                        if(empty($read[$requestobj[$fortyhc]]) == true){
                                            $fortyhcVal = '0';
                                        } else{                                  
                                            $fortyhcVal = $read[$requestobj[$fortyhc]];
                                        }
                                    }

                                    if( $fortynorExiBol == true){
                                        if($statusexistfortynor == 1){
                                            if(empty($read[$requestobj[$fortynor]]) == true){
                                                $fortynorVal = '0';
                                            } else {
                                                $fortynorVal = $read[$requestobj[$fortynor]];
                                            }
                                        }
                                    }

                                    if( $fortyfiveExiBol == true){
                                        if($statusexistfortyfive == 1){
                                            if(empty($read[$requestobj[$fortyfive]]) == true){
                                                $fortyfiveVal = '0';
                                            } else {
                                                $fortyfiveVal = $read[$requestobj[$fortyfive]];
                                            }
                                        }
                                    }

                                    if( $variantecurrency == true){
                                        if($requestobj[$statustypecurren] == 2){
                                            //------------ PARA RATES ------------------------
                                            $currencyobj = Currency::find($currencyValtwen);
                                            $currencyVal = $currencyobj['alphacode'];

                                            //------------- PARA SURCHARGERS -----------------

                                            if($curreExitwenBol == true){
                                                $currencyTWobj   = Currency::find($currencyValtwen);
                                                $currencyValtwen = $currencyTWobj['alphacode'];
                                            }

                                            if($curreExiforBol == true){
                                                $currencyFORobj  = Currency::find($currencyValfor);
                                                $currencyValfor  = $currencyFORobj['alphacode'];
                                            }

                                            if($curreExiforHCBol == true){
                                                $currencyFORHCobj  = Currency::find($currencyValforHC);
                                                $currencyValforHC  = $currencyFORHCobj['alphacode'];
                                            }

                                            if($curreExifornorBol == true){
                                                $currencyFORnorobj  = Currency::find($currencyValfornor);
                                                $currencyValfornor  = $currencyFORnorobj['alphacode'];
                                            }

                                            if($curreExiforfiveBol == true){
                                                $currencyFORfiveobj  = Currency::find($currencyValforfive);
                                                $currencyValforfive  = $currencyFORfiveobj['alphacode'];
                                            }

                                        } else {
                                            $currencyobj = Currency::find($currencyVal);
                                            $currencyVal = $currencyobj['alphacode'];
                                        }
                                    } 

                                    //---------------------------- CALCULATION TYPE -------------------------------------------------

                                    if($calculationtypeExiBol == true){
                                        $calculationType = CalculationType::find($calculationtypeVal);
                                        $calculationtypeVal = $calculationType['name'];
                                    }

                                    //---------------------------- TYPE -------------------------------------------------------------

                                    if($typeExiBol == true){
                                        $Surchargeobj = Surcharge::find($surchargeVal);
                                        $surchargeVal = $Surchargeobj['name'];
                                    }

                                    //////////////////////////////////////////////////////////////////////////////////////////////

                                    // Globalchargers Fallidos
                                    if($calculationtypeExiBol == true){
                                        //
                                        if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_CONTAINER') == 0){
                                            // son tres cargas Per 20, Per 40, Per 40'HC

                                            if($originBol == true || $destinyBol == true){
                                                foreach($randons as  $rando){
                                                    //insert por arreglo de puerto
                                                    if($originBol == true ){
                                                        if($differentiatorBol){
                                                            $originerr = Country::find($rando);
                                                        } else {
                                                            $originerr = Harbor::find($rando);
                                                        }
                                                        $originVal = $originerr['name'];
                                                        if($destiExitBol == true){    
                                                            $destinyVal = $read[$requestobj[$destinyExc]];
                                                        }
                                                    } else {
                                                        if($differentiatorBol){
                                                            $destinyerr = Country::find($rando);
                                                        } else {
                                                            $destinyerr = Harbor::find($rando);
                                                        }
                                                        $destinyVal = $destinyerr['name'];
                                                        if($origExiBol == true){
                                                            $originVal = $read[$requestobj[$originExc]];                                      
                                                        }
                                                    }
                                                    // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                                    if($statusexistfortynor == 1){
                                                        $fortynorif =  $read[$requestobj[$fortynor]];
                                                    } else {
                                                        $fortynorif = $read[$requestobj[$twenty]];
                                                    }

                                                    if($statusexistfortyfive == 1){ 
                                                        $fortyfiveif = $read[$requestobj[$fortyfive]];
                                                    }else {
                                                        $fortyfiveif = $read[$requestobj[$twenty]];
                                                    }

                                                    if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                                       $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                                       $read[$requestobj[$fortyhc]] == $fortynorif &&
                                                       $fortynorif == $fortyfiveif){

                                                        // -------- PER_CONTAINER -------------------------
                                                        // se almacena uno solo porque todos los valores son iguales

                                                        $calculationtypeValfail = 'Per Container';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }

                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $twentyVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        //$ratescollection->push($ree);

                                                    } else{


                                                        // -------- 20' ---------------------------------

                                                        $calculationtypeValfail = 'Per 20 "';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }

                                                        if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$twentyVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $twentyVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        // $ratescollection->push($ree);

                                                        // -------- 40' ---------------------------------

                                                        $calculationtypeValfail = 'Per 40 "';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfor;
                                                        }

                                                        if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$fortyVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $fortyVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        // $ratescollection->push($ree);

                                                        // -------- 40'HC -------------------------------

                                                        $calculationtypeValfail = 'Per 40 HC';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforHC;
                                                        }

                                                        if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$fortyhcVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $fortyhcVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        //$ratescollection->push($ree);

                                                        // -------- 40'NOR -------------------------------

                                                        $calculationtypeValfail = 'Per 40 NOR';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValfornor;
                                                        }

                                                        if($fortynorVal != 0 || $fortynorVal != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$fortynorVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $fortynorVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        //$ratescollection->push($ree);

                                                        // -------- 45' ---------------------------------

                                                        $calculationtypeValfail = 'Per 45';

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValforfive;
                                                        }

                                                        if($fortyfiveVal != 0 || $fortyfiveVal != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$fortyfiveVal)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $fortyfiveVal, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                            }
                                                        }
                                                        //$ratescollection->push($ree);

                                                    }
                                                }
                                            } else {
                                                if($origExiBol == true){
                                                    if($differentiatorBol == true){
                                                        $originExits = Country::find($originVal);
                                                        $originVal = $originExits['name'];     
                                                    } else {
                                                        $originExits = Harbor::find($originVal);
                                                        $originVal = $originExits->name;                                       
                                                    }
                                                }
                                                if($destiExitBol == true){ 
                                                    if($differentiatorBol == true){
                                                        $destinyExits = Country::find($destinyVal);
                                                        $destinyVal = $destinyExits['name'];
                                                    } else {
                                                        $destinyExits = Harbor::find($destinyVal);
                                                        $destinyVal = $destinyExits->name;
                                                    }
                                                }

                                                // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                                if($statusexistfortynor == 1){
                                                    $fortynorif =  $read[$requestobj[$fortynor]];
                                                } else {
                                                    $fortynorif = $read[$requestobj[$twenty]];
                                                }

                                                if($statusexistfortyfive == 1){ 
                                                    $fortyfiveif = $read[$requestobj[$fortyfive]];
                                                }else {
                                                    $fortyfiveif = $read[$requestobj[$twenty]];
                                                }
                                                if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                                   $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                                   $read[$requestobj[$fortyhc]] == $fortynorif &&
                                                   $fortynorif == $fortyfiveif){

                                                    // -------- PER_CONTAINER -------------------------
                                                    // se almacena uno solo porque todos los valores son iguales

                                                    $calculationtypeValfail = 'Per Container';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }
                                                    if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $twentyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                } else{

                                                    // -------- 20' ---------------------------------

                                                    $calculationtypeValfail = 'Per 20 "';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }

                                                    if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $twentyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }
                                                    // -------- 40' ---------------------------------

                                                    $calculationtypeValfail = 'Per 40 "';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    }

                                                    if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);

                                                            // $ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40'HC -------------------------------

                                                    $calculationtypeValfail = 'Per 40 HC';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    }

                                                    if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyhcVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyhcVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);

                                                            //  $ratescollection->push($ree);
                                                        }
                                                    }
                                                    // -------- 40'NOR ------------------------------

                                                    $calculationtypeValfail = 'Per 40 NOR';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfornor;
                                                    }

                                                    if($fortynorVal != 0 || $fortynorVal != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortynorVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortynorVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);

                                                            //  $ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 45' ---------------------------------

                                                    $calculationtypeValfail = 'Per 45';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforfive;
                                                    }

                                                    if($fortyfiveVal != 0 || $fortyfiveVal != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyfiveVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyfiveVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //  $ratescollection->push($ree);
                                                        }
                                                    }
                                                }
                                            }

                                        } 
                                        else{
                                            if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'Per Shipment') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'PER_BL') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TON') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'PER_TEU') == 0){

                                                if(strnatcasecmp($read[$requestobj[$CalculationType]],'PER_SHIPMENT') == 0 || strnatcasecmp($read[$requestobj[$CalculationType]],'Per Shipment') == 0){
                                                    $calculationtypeValfail = 'Per Shipment';
                                                } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_BL') == 0 ){
                                                    $calculationtypeValfail = 'Per BL';
                                                } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TON') == 0){
                                                    $calculationtypeValfail = 'Per TON';
                                                } else if(strnatcasecmp($read[$requestobj[$CalculationType]],'Per_TEU') == 0){
                                                    $calculationtypeValfail = 'Per TEU';
                                                }

                                                // multiples puertos o por seleccion
                                                if($originBol == true || $destinyBol == true){
                                                    foreach($randons as  $rando){
                                                        //insert por arreglo de puerto
                                                        if($originBol == true ){
                                                            if($differentiatorBol){
                                                                $originerr = Country::find($rando);
                                                            } else {
                                                                $originerr = Harbor::find($rando);
                                                            }
                                                            $originVal = $originerr['name'];
                                                            if($destiExitBol == true){    
                                                                $destinyVal = $read[$requestobj[$destinyExc]];
                                                            }
                                                        } else {
                                                            if($differentiatorBol){
                                                                $destinyerr = Country::find($rando);
                                                            } else {
                                                                $destinyerr = Harbor::find($rando);
                                                            }
                                                            $destinyVal = $destinyerr['name'];
                                                            if($origExiBol == true){
                                                                $originVal = $read[$requestobj[$originExc]];                                      
                                                            }
                                                        }

                                                        if($requestobj[$statustypecurren] == 2){
                                                            $currencyVal = $currencyValtwen;
                                                        }

                                                        if($twentyVal != 0 || $twentyVal != 0.0){
                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValtwen;
                                                            } 
                                                            $ammount = $twentyVal;

                                                        } else if ($fortyVal != 0 || $fortyVal != 0.0){
                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValfor;
                                                            } 
                                                            $ammount = $fortyVal;

                                                        }else if($fortyhcVal != 0 ||$fortyhcVal != 0.0){

                                                            if($requestobj[$statustypecurren] == 2){
                                                                $currencyVal = $currencyValforHC;
                                                            } 
                                                            $ammount = $fortyhcVal;

                                                        }else if($fortynorVal != 0 || $fortynorVal != 0.0){

                                                            if($statusexistfortynor == 1){
                                                                if($requestobj[$statustypecurren] == 2){
                                                                    $currencyVal = $currencyValfornor;
                                                                } 
                                                            }
                                                            $ammount = $fortynorVal;

                                                        }else {
                                                            if($statusexistfortyfive == 1){
                                                                if($requestobj[$statustypecurren] == 2){
                                                                    $currencyVal = $currencyValforfive;
                                                                } 
                                                            }
                                                            $ammount = $fortyfiveVal;
                                                        }

                                                        if($ammount != 0 || $ammount != 0.0){
                                                            $extgc = null;
                                                            $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                                ->where('origin',$originVal)
                                                                ->where('destiny',$destinyVal)
                                                                ->where('typedestiny',$typedestinyVal)
                                                                ->where('calculationtype',$calculationtypeValfail)
                                                                ->where('ammount',$ammount)
                                                                ->where('currency',$currencyVal)
                                                                ->where('carrier',$carrierVal)
                                                                ->where('validityto',$validitytoVal)
                                                                ->where('validityfrom',$validityfromVal)
                                                                ->where('port',true)
                                                                ->where('country',false)
                                                                ->where('company_user_id',$companyUserIdVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();

                                                            if(count($extgc) == 0){
                                                                FailedGlobalcharge::create([
                                                                    'surcharge'       	=> $surchargeVal,
                                                                    'origin'          	=> $originVal,
                                                                    'destiny'          	=> $destinyVal,
                                                                    'typedestiny'     	=> $typedestinyVal,
                                                                    'calculationtype'	=> $calculationtypeValfail,  //////
                                                                    'ammount'           => $ammount, //////
                                                                    'currency'		    => $currencyVal, //////
                                                                    'carrier'	        => $carrierVal,
                                                                    'validityto'	    => $validitytoVal,
                                                                    'validityfrom'      => $validityfromVal,
                                                                    'port'        		=> true,// por defecto
                                                                    'country'        	=> false,// por defecto
                                                                    'company_user_id'   => $companyUserIdVal,
                                                                    'account_id'        => $account_idVal,
                                                                    'differentiator'   => $differentiatorVal
                                                                ]);
                                                                //$ratescollection->push($ree);                    
                                                            }
                                                        }

                                                    }
                                                } else {
                                                    // puertos leidos del excel
                                                    if($origExiBol == true){
                                                        if($differentiatorBol == true){
                                                            $originExits = Country::find($originVal);
                                                            $originVal = $originExits['name'];     
                                                        } else {
                                                            $originExits = Harbor::find($originVal);
                                                            $originVal = $originExits->name;                                       
                                                        }
                                                    }
                                                    if($destiExitBol == true){ 
                                                        if($differentiatorBol == true){
                                                            $destinyExits = Country::find($destinyVal);
                                                            $destinyVal = $destinyExits['name'];
                                                        } else {
                                                            $destinyExits = Harbor::find($destinyVal);
                                                            $destinyVal = $destinyExits->name;
                                                        }
                                                    }

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }
                                                    if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $twentyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //  $ratescollection->push($ree);
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                    } else{
                                        // se deconoce si es PER_CONTAINER O PER_DOC

                                        if($originBol == true || $destinyBol == true){
                                            foreach($randons as  $rando){
                                                //insert por arreglo de puerto
                                                if($originBol == true ){
                                                    if($differentiatorBol){
                                                        $originerr = Country::find($rando);
                                                    } else {
                                                        $originerr = Harbor::find($rando);
                                                    }
                                                    $originVal = $originerr['name'];
                                                    if($destiExitBol == true){    
                                                        $destinyVal = $read[$requestobj[$destinyExc]];
                                                    }
                                                } else {
                                                    if($differentiatorBol){
                                                        $destinyerr = Country::find($rando);
                                                    } else {
                                                        $destinyerr = Harbor::find($rando);
                                                    }
                                                    $destinyVal = $destinyerr['name'];
                                                    if($origExiBol == true){
                                                        $originVal = $read[$requestobj[$originExc]];                                      
                                                    }
                                                }
                                                // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                                if($statusexistfortynor == 1){
                                                    $fortynorif =  $read[$requestobj[$fortynor]];
                                                } else {
                                                    $fortynorif = $read[$requestobj[$twenty]];
                                                }

                                                if($statusexistfortyfive == 1){ 
                                                    $fortyfiveif = $read[$requestobj[$fortyfive]];
                                                }else {
                                                    $fortyfiveif = $read[$requestobj[$twenty]];
                                                }
                                                if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                                   $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                                   $read[$requestobj[$fortyhc]] == $fortynorif &&
                                                   $fortynorif == $fortyfiveif){

                                                    // -------- PER_CONTAINER -------------------------
                                                    // se almacena uno solo porque todos los valores son iguales

                                                    $calculationtypeValfail = 'Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }

                                                    if($twentyArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $twentyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'    => $differentiatorVal
                                                            ]);
                                                            // $ratescollection->push($ree);
                                                        }
                                                    }

                                                } else{

                                                    // -------- 20' ---------------------------------

                                                    $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValtwen;
                                                    }
                                                    if($twentyArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$twentyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $twentyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            // $ratescollection->push($ree);
                                                        }
                                                    }
                                                    // -------- 40' ---------------------------------

                                                    $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfor;
                                                    }

                                                    if($fortyArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40'HC -------------------------------

                                                    $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforHC;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyhcVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyhcVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);

                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 40'NOR ------------------------------

                                                    $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValfornor;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortynorVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortynorVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'   => $differentiatorVal
                                                            ]);
                                                            //$ratescollection->push($ree);
                                                        }
                                                    }

                                                    // -------- 45'  -------------------------------

                                                    $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                                    if($requestobj[$statustypecurren] == 2){
                                                        $currencyVal = $currencyValforfive;
                                                    }

                                                    if($fortyhcArr[0] != 0){
                                                        $extgc = null;
                                                        $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                            ->where('origin',$originVal)
                                                            ->where('destiny',$destinyVal)
                                                            ->where('typedestiny',$typedestinyVal)
                                                            ->where('calculationtype',$calculationtypeValfail)
                                                            ->where('ammount',$fortyfiveVal)
                                                            ->where('currency',$currencyVal)
                                                            ->where('carrier',$carrierVal)
                                                            ->where('validityto',$validitytoVal)
                                                            ->where('validityfrom',$validityfromVal)
                                                            ->where('port',true)
                                                            ->where('country',false)
                                                            ->where('company_user_id',$companyUserIdVal)
                                                            ->where('differentiator',$differentiatorVal)
                                                            ->get();

                                                        if(count($extgc) == 0){
                                                            FailedGlobalcharge::create([
                                                                'surcharge'       	=> $surchargeVal,
                                                                'origin'          	=> $originVal,
                                                                'destiny'          	=> $destinyVal,
                                                                'typedestiny'     	=> $typedestinyVal,
                                                                'calculationtype'	=> $calculationtypeValfail,  //////
                                                                'ammount'           => $fortyfiveVal, //////
                                                                'currency'		    => $currencyVal, //////
                                                                'carrier'	        => $carrierVal,
                                                                'validityto'	    => $validitytoVal,
                                                                'validityfrom'      => $validityfromVal,
                                                                'port'        		=> true,// por defecto
                                                                'country'        	=> false,// por defecto
                                                                'company_user_id'   => $companyUserIdVal,
                                                                'account_id'        => $account_idVal,
                                                                'differentiator'    => $differentiatorVal
                                                            ]);

                                                            //$ratescollection->push($ree);
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if($origExiBol == true){
                                                if($differentiatorBol == true){
                                                    $originExits = Country::find($originVal);
                                                    $originVal = $originExits['name'];     
                                                } else {
                                                    $originExits = Harbor::find($originVal);
                                                    $originVal = $originExits->name;                                       
                                                }
                                            }
                                            if($destiExitBol == true){ 
                                                if($differentiatorBol == true){
                                                    $destinyExits = Country::find($destinyVal);
                                                    $destinyVal = $destinyExits['name'];
                                                } else {
                                                    $destinyExits = Harbor::find($destinyVal);
                                                    $destinyVal = $destinyExits->name;
                                                }
                                            }

                                            // verificamos si todos los valores son iguales para crear unos solo como PER_CONTAINER

                                            if($statusexistfortynor == 1){
                                                $fortynorif =  $read[$requestobj[$fortynor]];
                                            } else {
                                                $fortynorif = $read[$requestobj[$twenty]];
                                            }

                                            if($statusexistfortyfive == 1){ 
                                                $fortyfiveif = $read[$requestobj[$fortyfive]];
                                            }else {
                                                $fortyfiveif = $read[$requestobj[$twenty]];
                                            }

                                            if($read[$requestobj[$twenty]] == $read[$requestobj[$forty]] &&
                                               $read[$requestobj[$forty]]  == $read[$requestobj[$fortyhc]] &&
                                               $read[$requestobj[$fortyhc]] == $fortynorif &&
                                               $fortynorif == $fortyfiveif){

                                                // -------- PER_CONTAINER -------------------------
                                                // se almacena uno solo porque todos los valores son iguales

                                                $calculationtypeValfail = 'Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$twentyVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $twentyVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }


                                            } else{

                                                // -------- 20' ---------------------------------

                                                $calculationtypeValfail = 'Per 20 "Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValtwen;
                                                }

                                                if($twentyArr[0] != 0 || $twentyArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$twentyVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $twentyVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }

                                                // -------- 40' ---------------------------------

                                                $calculationtypeValfail = 'Per 40 "Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfor;
                                                }

                                                if($fortyArr[0] != 0 || $fortyArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$fortyVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $fortyVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }

                                                // -------- 40'HC -------------------------------

                                                $calculationtypeValfail = '40HC Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforHC;
                                                }

                                                if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$fortyhcVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $fortyhcVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }

                                                // -------- 40'NOR -------------------------------

                                                $calculationtypeValfail = '40\'NOR Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValfornor;
                                                }

                                                if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$fortynorVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $fortynorVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }

                                                // -------- 45' ---------------------------------

                                                $calculationtypeValfail = '45\' Error fila '.$i.'_E_E';

                                                if($requestobj[$statustypecurren] == 2){
                                                    $currencyVal = $currencyValforfive;
                                                }

                                                if($fortyhcArr[0] != 0 || $fortyhcArr[0] != 0.0){
                                                    $extgc = null;
                                                    $extgc = FailedGlobalcharge::where('surcharge',$surchargeVal)
                                                        ->where('origin',$originVal)
                                                        ->where('destiny',$destinyVal)
                                                        ->where('typedestiny',$typedestinyVal)
                                                        ->where('calculationtype',$calculationtypeValfail)
                                                        ->where('ammount',$fortyfiveVal)
                                                        ->where('currency',$currencyVal)
                                                        ->where('carrier',$carrierVal)
                                                        ->where('validityto',$validitytoVal)
                                                        ->where('validityfrom',$validityfromVal)
                                                        ->where('port',true)
                                                        ->where('country',false)
                                                        ->where('company_user_id',$companyUserIdVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();

                                                    if(count($extgc) == 0){
                                                        FailedGlobalcharge::create([
                                                            'surcharge'       	=> $surchargeVal,
                                                            'origin'          	=> $originVal,
                                                            'destiny'          	=> $destinyVal,
                                                            'typedestiny'     	=> $typedestinyVal,
                                                            'calculationtype'	=> $calculationtypeValfail,  //////
                                                            'ammount'           => $fortyfiveVal, //////
                                                            'currency'		    => $currencyVal, //////
                                                            'carrier'	        => $carrierVal,
                                                            'validityto'	    => $validitytoVal,
                                                            'validityfrom'      => $validityfromVal,
                                                            'port'        		=> true,// por defecto
                                                            'country'        	=> false,// por defecto
                                                            'company_user_id'   => $companyUserIdVal,
                                                            'account_id'        => $account_idVal,
                                                            'differentiator'   => $differentiatorVal
                                                        ]);
                                                        //$ratescollection->push($ree);
                                                    }
                                                }
                                            }
                                        }
                                    }


                                    $falli++;
                                    //echo $i;
                                    //dd($ratescollection);

                                }
                            }
                        }
                    }
                    //-------------------------- fin distinto del primer ciclo
                    $i++;
                }

                //dd('Todo se cargo, surcharges o rates fallidos: '.$falli);
            });
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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
        //
    }

    public function indexAccount(){
        $account = AccountImportationGlobalChargerLcl::with('companyuser')->get();
        return DataTables::of($account)
            ->addColumn('status', function ( $account) {
                return  $account->status;

            })
            ->addColumn('company_user_id', function ( $account) {
                return  $account->companyuser->name;
            })
            ->addColumn('requestgc_id', function ( $account) {
                if(empty($account->requestgc_id) != true){
                    return  $account->requestgc_id;
                } else {
                    return 'Manual';
                }
            })
            ->addColumn('action', function ( $account) {
                return '<a href="/ImportationGlobalchargesFcl/FailedGlobalchargers/'.$account->id.'/1" class="show"  title="Failed-Good" >
                            <samp class="la la-pencil-square-o" style="font-size:20px; color:#031B4E"></samp>
                        </a>
                        &nbsp;
                        &nbsp;
                        <a href="/ImportationGlobalchargesFcl/DownloadAccountgcfcl/'.$account->id.'" class="">
                            <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E" title="Download"></samp>
                        </a>
                        &nbsp; &nbsp; 
                        <a href="#" class="eliminaracount" data-id-acount="'.$account->id.'"  title="Delete" >
                            <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                        </a>';
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }
}
