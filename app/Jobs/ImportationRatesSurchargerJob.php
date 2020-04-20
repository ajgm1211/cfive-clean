<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Rate;
use App\User;
use PrvHarbor;
use App\Region;
use App\Harbor;
use PrvCarrier;
use App\Company;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Contract;
use App\FailRate;
use App\Currency;
use App\Surcharge;
use App\LocalCharge;
use App\TypeDestiny;
use App\ScheduleType;
use App\LocalCharPort;
use App\FailSurCharge;
use App\CalculationType;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use Illuminate\Http\Request;
use App\ContractUserRestriction;
use App\Notifications\N_general;
use App\ContractCompanyRestriction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;

use HelperAll;
use App\Container;
use App\GroupContainer;
use App\ContainerCalculation;
use Illuminate\Support\Facades\DB;
use App\MyClass\Excell\MyReadFilter;
use Spatie\MediaLibrary\MediaStream;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\Models\Media;
use App\MyClass\Excell\ChunkReadFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\NewContractRequest as RequestFcl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\AccountImportationContractFcl as AccountFcl;

class ImportationRatesSurchargerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $account_id,$contract_id,$user_id;

    public function __construct($account_id,$contract_id,$user_id)
    {
        $this->account_id      = $account_id;
        $this->contract_id     = $contract_id;
        $this->user_id         = $user_id;

    }

    public function handle()
    {

        $account_id             = $this->account_id;
        $account                = AccountFcl::find($account_id);
        $json_account_dc        = json_decode($account->data,true);
        $valuesSelecteds        = $json_account_dc['valuesSelecteds'];
        $final_columns          = $json_account_dc['final_columns'];
        //dd($valuesSelecteds,$final_columns);

        $contract_id            = $valuesSelecteds['contract_id'];
        $groupContainer_id      = $valuesSelecteds['group_container_id'];
        $column_calculatioT_bol = true;
        $caracteres             = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':','1','2','3','4','5','6','7','8','9','0'];

        // LOAD CALCULATIONS FOR COLUMN ------------------------
        $contenedores_to_cal = Container::where('gp_container_id',$groupContainer_id)->get();
        $conatiner_calculation_id = [];
        foreach($contenedores_to_cal as $row_cont_calcult){
            $contenedores_calcult =  null;
            //$contenedores_calcult =  ContainerCalculation::where('container_id',10)
            $contenedores_calcult =  ContainerCalculation::where('container_id',$row_cont_calcult->id)
                ->whereHas('calculationtype', function ( $query) {
                    $query->where('gp_pcontainer',true);
                })->get();
            //dd($contenedores_to_cal,$row_cont_calcult->code,$contenedores_calcult);
            if(count($contenedores_calcult) == 1){
                foreach($contenedores_calcult as $contenedor_calcult){   
                    $conatiner_calculation_id[$row_cont_calcult->code] = $contenedor_calcult->calculationtype_id;
                }
            } else if(count($contenedores_calcult) > 1 || count($contenedores_calcult) == 0){
                $column_calculatioT_bol = false;
            }
        }
        //dd($conatiner_calculation_id);

        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        $mediaItem  = $account->getFirstMedia('document');
        $excel      = Storage::disk('FclAccount')->get($mediaItem->id.'/'.$mediaItem->file_name);
        Storage::disk('FclImport')->put($mediaItem->file_name,$excel);
        $excelF     = Storage::disk('FclImport')->url($mediaItem->file_name);

        $extObj     = new \SplFileInfo($mediaItem->file_name);
        $ext        = $extObj->getExtension();

        if(strnatcasecmp($ext,'xlsx')==0){
            $inputFileType = 'Xlsx';
        } else if(strnatcasecmp($ext,'xls')==0){
            $inputFileType = 'Xls';
        } else {
            $inputFileType = 'Csv';
        }
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($excelF);
        $writer = IOFactory::createWriter($spreadsheet, "Csv");
        $writer->setSheetIndex(0);
        $excelF = str_replace($ext,'csv',$excelF);
        $inputFileType = 'Csv';
        $writer->save($excelF);
        //dd($excelF,$extObj,$ext);
        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        if($column_calculatioT_bol){
            $chunkRow   =  new ChunkReadFilter();

            $readerJob  = IOFactory::createReader($inputFileType);
            $readerJob->setReadDataOnly(true);
            //$readerJob->setReadFilter($chunkRow);

            $chunkSize = 2;

            $spreadsheetJob = $readerJob->load($excelF);
            $sheetData = $spreadsheetJob->getActiveSheet()->toArray();
            //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray());

            $originExc              = $final_columns["ORIGIN"];// lectura de excel
            $destinyExc             = $final_columns["DESTINY"];// lectura de excel
            $chargeExc              = $final_columns["CHARGE"];// lectura de excel
            $calculationtypeExc     = $final_columns["CALCULATION TYPE"];// lectura de excel
            $chargeExc              = $final_columns["CHARGE"];// lectura de excel

            $company_user_id        = $valuesSelecteds['company_user_id'];
            $statusPortCountry      = $valuesSelecteds['select_portCountryRegion'];
            $statusTypeDestiny      = $valuesSelecteds['select_typeDestiny'];
            $statusCarrier          = $valuesSelecteds['select_carrier'];
            $chargeVal              = $valuesSelecteds['chargeVal'];
            $request_columns        = $valuesSelecteds['request_columns'];
            $statusCurrency         = $valuesSelecteds['select_currency'];

            $currencyVal            = '';

            // DIFERENCIADOR DE PUERTO CONTRY/REGION ---------------
            if($statusPortCountry){
                $differentiator = $final_columns["DIFFERENTIATOR"];            
            }

            // CURRENCY --------------------------------------------
            if($statusCurrency == 3){
                $currencyVal    = $valuesSelecteds['currencyVal'];            
            } else if($statusCurrency == 1){
                $currencyExc    = $final_columns["CURRENCY"];                        
            }

            // TYPE DESTINY ----------------------------------------
            if(!$statusTypeDestiny){
                $typedestinyExc     = $final_columns["TYPE DESTINY"];            
            }

            if(!$statusCarrier){
                $carrierExc     = $final_columns["CARRIER"];            
            }
            $columns_rt_ident = [];
            if($groupContainer_id == 1){
                $contenedores_rt = Container::where('gp_container_id',$groupContainer_id)->where('options->column',true)->get();
                foreach($contenedores_rt as $conten_rt){
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
            }

            $countRow = 1;
            foreach($sheetData as $row){
                if($countRow > 1){
                    //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray(),$row);

                    //------------------ COLUMNS SELECTEDS VALUES/CURRENCY/OPTIONS ----------------------------
                    $contenedores = Container::where('gp_container_id',$groupContainer_id)->get();
                    $columna_cont = [];
                    $currency_bol = [];
                    foreach($contenedores as $contenedor){
                        $options_cont = null;
                        $options_cont = json_decode($contenedor->options);
                        if(in_array($contenedor->code,$request_columns)){ // Asociamos en una matriz llaves Valores y moneda que exista en la seleccion
                            if($statusCurrency == 3){ //currency seleccionado en el panel(select) no hay columna en el excel
                                $value_ = null;
                                $value_ = floatval($row[$final_columns[$contenedor->code]]);
                                $columna_cont[$contenedor->code] = [$value_,$currencyVal,$options_cont->optional,false,$options_cont->column];
                                $currency_bol[$contenedor->code] = true;
                            } else if($statusCurrency == 2){ // valor y currency en la misma columna del excel
                                $value_arr = null;
                                $value_arr = explode(' ',$row[$final_columns[$contenedor->code]]);
                                if(count($value_arr) == 1){
                                    array_push($value_arr,'_E_E');
                                    array_push($value_arr,$options_cont->optional);
                                    array_push($value_arr,false);
                                    array_push($value_arr,$options_cont->column);
                                    $currency_bol[$contenedor->code] = false;
                                    $value_arr[0] = floatval($value_arr[0]);
                                    $columna_cont[$contenedor->code] = $value_arr;
                                } else if(count($value_arr) > 1){
                                    $curren_obj = Currency::where('alphacode','=',$value_arr[1])->first();
                                    if(!empty($curren_obj->id)){
                                        $value_arr[1] = $curren_obj->id;
                                        if(count($value_arr) == 2){
                                            $currency_bol[$contenedor->code] = true;
                                        } else {
                                            $value_arr[1] = $value_arr[1].'_E_E'; 
                                            $currency_bol[$contenedor->code] = false;
                                        }

                                        if(count($value_arr) == 2){
                                            array_push($value_arr,$options_cont->optional);
                                            array_push($value_arr,false);
                                            array_push($value_arr,$options_cont->column);
                                        } else if(count($value_arr) == 3){
                                            $value_arr[2] = $options_cont->optional;
                                            array_push($value_arr,false);
                                            array_push($value_arr,$options_cont->column);
                                        } else if(count($value_arr) == 4){
                                            $value_arr[2] = $options_cont->optional;
                                            $value_arr[3] = false;
                                            array_push($value_arr,$options_cont->column);
                                        }
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = $value_arr;
                                    } else {
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = [$value_arr[0],$value_arr[1].'_E_E',$options_cont->optional,false,$options_cont->column];
                                        $currency_bol[$contenedor->code] = false;
                                    }
                                }
                            } else if($statusCurrency == 1){// columna sola de currency en el excel
                                $value_cur  = null;
                                $value_cur  = trim($row[$currencyExc]);
                                $curren_obj = Currency::where('alphacode','=',$value_cur)->first();
                                //                                try{
                                //                                    $curren_obj->id;
                                //                                } catch(\Exception $e){
                                //                                    dd($statusCurrency,$currencyExc,$value_cur,$curren_obj,empty($curren_obj->id));
                                //                                }
                                if(!empty($curren_obj->id)){
                                    $value_cur = $curren_obj->id;
                                    $currency_bol[$contenedor->code] = true;
                                } else {
                                    $value_cur = $value_cur.'_E_E';                                    
                                    $currency_bol[$contenedor->code] = false;
                                }
                                $columna_cont[$contenedor->code] = [floatval($row[$final_columns[$contenedor->code]]),$value_cur,$options_cont->optional,false,$options_cont->column];
                            }
                            //array_push($columna_cont[$contenedor->code],false);
                        } else { // Agregamos en una matriz llaves Valores y moneda que no existen en la seleccion pero si en el equipo Dry,RF,FR,OP....
                            $currency_bol[$contenedor->code] = true;
                            $columna_cont[$contenedor->code] = [0.00,149,$options_cont->optional,true,$options_cont->column];
                        }
                    }

                    //  0 --->  valor.
                    //  1 --->  moneda.
                    //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                    //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                    //  5 --->  la columna pertenece a una columna(true) o a un json (false).


                    //dd($columna_cont,$currency_bol,$statusCurrency);
                    //--- PORT/CONTRY/REGION BOOL -------------------------------------
                    $differentiatorVal = '';
                    if($statusPortCountry){
                        $differentiatorVal = $row[$differentiator];
                    } else {
                        $differentiatorVal = 'port';
                    }

                    //--- ORIGIN ------------------------------------------------------
                    $oricount = 0;
                    $originMultps = explode('|',$row[$originExc]);
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

                    //--- DESTINY -----------------------------------------------------
                    $descount = 0;
                    $destinyMultps = explode('|',$row[$destinyExc]);
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

                    //--- INICION DE ERECORRIDO POR | ---------------------------------
                    foreach($originMultps as $originMult){
                        foreach($destinyMultps as $destinyMult){

                            $originVal              = '';
                            $destinyVal             = '';
                            $carrierVal             = '';
                            $typedestinyVal         = '';
                            $surchargeVal           = '';
                            $calculationtypeVal     = '';

                            $differentiatorBol       = false;
                            $origExiBol              = false;
                            $destiExitBol            = false;
                            $typeExiBol              = false;
                            $carriExitBol            = false;
                            $typeChargeExiBol        = false;
                            $calculationtypeExiBol   = false;
                            $typedestinyExitBol      = false;

                            $calculation_type_exc   = null;
                            $chargeExc_val          = null;
                            $calculation_type_exc   = $row[$calculationtypeExc];
                            $chargeExc_val          = $row[$chargeExc];


                            //--------------- DIFRENCIADOR HARBOR COUNTRY ---------------------------------------------
                            if($statusPortCountry){
                                if(strnatcasecmp($differentiatorVal,'country') == 0 || strnatcasecmp($differentiatorVal,'region') == 0){
                                    $differentiatorBol = true;
                                } 
                            }

                            //--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------
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

                            //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
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

                            //--------------- Type Destiny ------------------------------------------------------------

                            if($statusTypeDestiny){
                                $typedestinyExitBol = true;
                                $typedestinyVal     = $valuesSelecteds['typeDestinyVal']; 
                            } else {
                                $typedestinyVal      = $row[$typedestinyExc]; // cuando el carrier existe en el excel
                                $typedestinyResul    = str_replace($caracteres,'',$typedestinyVal);
                                $typedestinyobj      = TypeDestiny::where('description','=',$typedestinyResul)->first();
                                if(empty($typedestinyobj->id) != true){
                                    $typedestinyExitBol = true;
                                    $typedestinyVal = $typedestinyobj->id;
                                }else{
                                    $typedestinyVal = $typedestinyVal.'_E_E';
                                }
                            }

                            //--------------- CARRIER -----------------------------------------------------------------
                            if($statusCarrier){
                                $carriExitBol   = true;
                                $carrierVal     = $valuesSelecteds['carrierVal']; // cuando se indica que no posee carrier 
                            } else {
                                $carrierVal     = $row[$carrierExc]; // cuando el carrier existe en el excel
                                $carrierArr     = PrvCarrier::get_carrier($carrierVal);
                                $carriExitBol   = $carrierArr['boolean'];
                                $carrierVal     = $carrierArr['carrier'];
                            }

                            //------------------ TYPE - CHARGE --------------------------------------------------------

                            if(!empty($chargeExc_val)){
                                $typeChargeExiBol = true;
                                if($chargeExc_val != $chargeVal){
                                    $surchargelist = Surcharge::where('name','=', $chargeExc_val)
                                        ->where('company_user_id','=', $company_user_id)
                                        ->first();
                                    if(empty($surchargelist) != true){
                                        $surchargeVal = $surchargelist['id'];
                                    }else{
                                        $surchargelist = Surcharge::create([
                                            'name'              => $chargeExc_val,
                                            'description'       => $chargeExc_val,
                                            'company_user_id'   => $company_user_id
                                        ]);
                                        $surchargeVal = $surchargelist->id;
                                    }
                                }
                            } else {
                                $surchargeVal = $chargeExc_val.'_E_E';
                            }

                            //------------------ CALCULATION TYPE -----------------------------------------------------
                            $calculationtype = null;
                            if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0 ||
                               strnatcasecmp($calculation_type_exc,'PER_TEU') == 0){
                                $calculationtype = CalculationType::where('options->name','=',$calculation_type_exc)
                                    ->whereHas('containersCalculation.container', function ($query) use($groupContainer_id){
                                        $query->whereHas('groupContainer', function ($queryTw) use($groupContainer_id){
                                            $queryTw->where('gp_container_id',$groupContainer_id);
                                        });
                                    })->get();
                            } else {
                                $calculationtype = CalculationType::where('options->name','=',$calculation_type_exc)->get();
                            }

                            if(count($calculationtype) == 1){
                                $calculationtypeExiBol = true;
                                $calculationtypeVal = $calculationtype[0]['id'];
                            } else if(count($calculationtype) > 1){
                                $calculationtypeVal = $calculation_type_exc.'F.R + '.count($calculationtype).' fila '.$countRow.'_E_E';
                            } else{
                                $calculationtypeVal = $calculation_type_exc.' fila '.$countRow.'_E_E';
                            }
                            //------------------ VALIDACION DE CAMPOS VACIOS COLUMNAS 20 40 ...------------------------

                            // AYUDANTES -----------------------

                            //                            $currency_bol = [];
                            //              $statusCurrency           = 2;
                            //              $currency_bol['20DV']     = false;
                            //              $columna_cont['20DV'][1]  = 'USDD_E_E';
                            //              //                            $currency_bol['40DV'] = true;
                            //              //                            $currency_bol['40HC'] = true;
                            //              $calculation_type_exc     = 'PER_SHIPMENT';
                            //              $calculationtypeVal       = 6;
                            //              //$calculationtypeVal       = 'PER_SHIPMENTss F.R + 0 fila'.$countRow.'_E_E';
                            //              $calculationtypeExiBol    = true;
                            //
                            //              //$columna_cont = [];
                            //              $columna_cont['20DV'][0] = 1.0;
                            //              $columna_cont['40DV'][0] = 15.0;
                            //              $columna_cont['40HC'][0] = 7.00;

                            // FIN - AYUDANTES ------------------

                            $values = true; 
                            $values_uniq = [];
                            foreach($columna_cont as $columnaRow){
                                array_push($values_uniq,floatval($columnaRow[0]));
                            }
                            if(count(array_unique($values_uniq)) == 1 
                               && $values_uniq[0] == 0.00){
                                $values = false;
                            }

                            //dd($columna_cont,$values);

                            //------------------ VALIDACION DE CURRENCY FALSE Ó TRUE 20 40 ...------------------------

                            $variant_currency = true; 
                            $currency_uniq = [];
                            foreach($currency_bol as $columnCurrenRow){
                                if($columnCurrenRow == true){
                                    array_push($currency_uniq,1);
                                } else{
                                    array_push($currency_uniq,0);
                                }
                            }
                            if(count(array_unique($currency_uniq)) > 1){
                                $variant_currency = false;
                            } else if(count(array_unique($currency_uniq)) == 1 
                                      && $currency_uniq[0] == 0){
                                $variant_currency = false;
                            }
                            //dd($currency_bol,$currency_uniq,array_unique($currency_uniq),$variant_currency);

                            $datos_finales = [
                                'originVal'             => $originVal,
                                'destinyVal'            => $destinyVal,
                                'typedestinyVal'        => $typedestinyVal,  
                                'carrierVal'            => $carrierVal,  
                                'surchargeVal'          => $surchargeVal,  
                                'calculationtypeVal'    => $calculationtypeVal,
                                'contract_id'           => $contract_id,
                                'chargeVal'             => $chargeVal,          // indica la diferencia entre "rate" o surcharge
                                'columnas_por_request'  => $request_columns,    // valores por columna, incluye el currency por columna
                                'valores_por_columna'   => $columna_cont,       // valores por columna, incluye el currency por columna:
                                //  0 --->  valor.
                                //  1 --->  moneda.
                                //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                                //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                                //  5 --->  la columna pertenece a una columna(true) o a un json (false).
                                'columns_rt_ident'      => $columns_rt_ident,  // contiene los nombres de las columnas de rates, DRY options->column = true
                                'currencyBol_por_colum' => $currency_bol,       // Arreglo de  currency por columna
                                '$calculation_type_exc' => $calculation_type_exc,// Columna Calculation del excel
                                'origExiBol'            => $origExiBol,         // true si encontro el valor origen
                                'destiExitBol'          => $destiExitBol,       // true si encontro el valor destino
                                'typedestinyExitBol'    => $typedestinyExitBol, // true si encontro el valor type destiny
                                'carriExitBol'          => $carriExitBol,       // true si encontro el valor carrier
                                'calculationtypeExiBol' => $calculationtypeExiBol, // true si encontro el valor calculation type
                                'values'                => $values,            // true si si todos los valore son distintos de cero
                                'typeChargeExiBol'      => $typeChargeExiBol,  // true si el valor es distinto de vacio
                                'variant_currency'      => $variant_currency,  // true si el encontro todos los currency, false si alguno de sus contenedores no tiene currency
                                'differentiatorBol'     => $differentiatorBol, // falso para port, true  para country o region
                                'statusPortCountry'     => $statusPortCountry, // true status de activacion port contry region, false port
                                'statusTypeDestiny'     => $statusTypeDestiny, // true para Seleccion desde panel, false para mapeo de excel 
                                'statusCarrier'         => $statusCarrier,     // true para seleccion desde el panel, falso para mapear excel 
                                'statusCurrency'        => $statusCurrency,     // 3. val. por SELECT,1. columna de  currency, 2. currency mas valor juntos
                                'conatiner_calculation_id' => $conatiner_calculation_id, // asocia los calculations con las columnas. relacion columna => calculation_id
                                'column_calculatioT_bol'   => $column_calculatioT_bol // False si falla la asociacion, true si esta asociado correctamente

                            ];
                            if(strnatcasecmp($chargeExc_val,$chargeVal) == 0 && $typedestinyExitBol == false){
                                $typedestinyExitBol = true;
                            }
                            //dd($datos_finales);

                            /////////////////////////////////

                            // INICIO IF PARA FALLIDOS O BUENOS
                            if($origExiBol              == true 
                               && $destiExitBol         == true
                               && $typedestinyExitBol   == true 
                               && $carriExitBol         == true 
                               && $calculationtypeExiBol== true
                               && $values               == true 
                               && $variant_currency     == true 
                               && $typeChargeExiBol     == true){

                                ///////////////////////////////// GOOD

                                $container_json = null;

                                if(strnatcasecmp($chargeExc_val,$chargeVal) == 0){ // Rates 
                                    if($differentiatorBol == false){
                                        $twuenty_val    = 0;
                                        $forty_val      = 0;
                                        $fortyhc_val    = 0;
                                        $fortynor_val   = 0;
                                        $fortyfive_val  = 0;
                                        $currency_val   = null;

                                        if($groupContainer_id != 1){ //DISTINTO A DRY
                                            foreach($columna_cont as $key => $conta_row){
                                                if($conta_row[4] == false){
                                                    $container_json['C'.$key] = ''.$conta_row[0];
                                                }              
                                                $currency_val = $conta_row[1];
                                            }
                                            $container_json = json_encode($container_json);

                                        } else { // DRY
                                            foreach($columna_cont as $key => $conta_row){
                                                if($conta_row[4] == false){ // columna contenedores
                                                    $container_json['C'.$key] = ''.$conta_row[0];
                                                } else{ // por columna específica
                                                    if(strnatcasecmp($columns_rt_ident[$key],'twuenty') == 0){
                                                        $twuenty_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'forty') == 0){
                                                        $forty_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortyhc') == 0){
                                                        $fortyhc_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortynor') == 0){
                                                        $fortynor_val = $conta_row[0];
                                                    } else if(strnatcasecmp($columns_rt_ident[$key],'fortyfive') == 0){
                                                        $fortyfive_val = $conta_row[0];                                        
                                                    }
                                                }  
                                                $currency_val = $conta_row[1];
                                            }
                                            $container_json = json_encode($container_json);

                                        }
                                        $exists = null;
                                        $exists = Rate::where('origin_port',$originVal)
                                            ->where('destiny_port',$destinyVal)
                                            ->where('carrier_id',$carrierVal)
                                            ->where('contract_id',$contract_id)
                                            ->where('twuenty',$twuenty_val)
                                            ->where('forty',$forty_val)
                                            ->where('fortyhc',$fortyhc_val)
                                            ->where('fortynor',$fortynor_val)
                                            ->where('fortyfive',$fortyfive_val)
                                            ->where('containers',$container_json)
                                            ->where('currency_id',$currency_val)
                                            ->get();
                                        //dd($twuenty_val,$forty_val,$fortyhc_val,$fortynor_val,$fortyfive_val,$container_json,$currency_val,$exists);
                                        if(count($exists) == 0){
                                            $ratesArre =  Rate::create([
                                                'origin_port'       => $originVal,
                                                'destiny_port'      => $destinyVal,
                                                'carrier_id'        => $carrierVal,
                                                'contract_id'       => $contract_id,
                                                'twuenty'           => $twuenty_val,
                                                'forty'             => $forty_val,
                                                'fortyhc'           => $fortyhc_val,
                                                'fortynor'          => $fortynor_val,
                                                'fortyfive'         => $fortyfive_val,
                                                'containers'        => $container_json,
                                                'currency_id'       => $currency_val
                                            ]);
                                        }
                                    }
                                } else { //Surcharges

                                    if($differentiatorBol == false){ //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'localcharports';
                                    }else {  //si es country verificamos si exite uno creado con country 
                                        $typeplace = 'localcharcountries';
                                    }

                                    if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0){

                                        // ESTOS ARREGLOS SON DE EJEMPLO PARA IGUALDAD DE VALORES EN PER_CONTAINER / Solo condicional -------
                                        //$columna_cont['20DV'][0]    = 1200;
                                        //$columna_cont['20DV'][3]    = false;
                                        //$columna_cont['40DV'][0]    = 1200;
                                        //$columna_cont['40HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][0]   = 1200;
                                        //$columna_cont['45HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][3]   = true;
                                        //$columna_cont['45HC'][3]    = true;

                                        // Comparamos si todos los valores son iguales (PER_CONTAINER) o si son distintos, dependiendo de equipo DRY,RF...
                                        $equals_values = [];
                                        $key = null;
                                        foreach($columna_cont as $key => $conta_row){
                                            if($conta_row[3] == true && $conta_row[2] != true){
                                                array_push($equals_values,$conta_row[0]);
                                            } else if($conta_row[3] == false){
                                                array_push($equals_values,$conta_row[0]); 
                                            }
                                        }
                                        //dd($columna_cont,$equals_values,array_unique($equals_values),count(array_unique($equals_values)));

                                        if(count(array_unique($equals_values)) == 1){ //Calculation PER_CONTAINER 1 solo registro
                                            $currency_val   = null;
                                            $ammount        = null;
                                            $key            = null;
                                            foreach($columna_cont as $key => $conta_row){
                                                $ammount        = $conta_row[0];
                                                $currency_val   = $conta_row[1];
                                                break;
                                            }

                                            if($ammount != 0 || $ammount != 0.00){
                                                //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                $surchargeObj = null;
                                                $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                                    ->where('typedestiny_id',$typedestinyVal)
                                                    ->where('contract_id',$contract_id)
                                                    ->where('calculationtype_id',$calculationtypeVal)
                                                    ->where('ammount',$ammount)
                                                    ->where('currency_id',$currency_val)
                                                    ->has($typeplace)
                                                    ->first();

                                                if(count($surchargeObj) == 0){
                                                    $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                        'surcharge_id'       => $surchargeVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal,
                                                        'ammount'            => $ammount,
                                                        'currency_id'        => $currency_val
                                                    ]);                                                    
                                                }

                                                //----------------------- CARRIERS -------------------------------------------
                                                $existsCar = null;
                                                $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                    ->where('localcharge_id',$surchargeObj->id)->first();
                                                if(count($existsCar) == 0){
                                                    LocalCharCarrier::create([ // tabla localcharcarriers
                                                        'carrier_id'        => $carrierVal,
                                                        'localcharge_id'    => $surchargeObj->id
                                                    ]);
                                                }

                                                //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                if($differentiatorBol){ // country
                                                    $existCount = null;
                                                    $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                        ->where('country_dest',$destinyVal)
                                                        ->where('localcharge_id',$surchargeObj->id)
                                                        ->first();
                                                    if(count($existCount) == 0){
                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                            'country_orig'      => $originVal,
                                                            'country_dest'      => $destinyVal,
                                                            'localcharge_id'    => $surchargeObj->id
                                                        ]);
                                                    }
                                                } else { // port
                                                    $existPort = null;
                                                    $existPort = LocalCharPort::where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('localcharge_id',$surchargeObj->id)
                                                        ->first();
                                                    if(count($existPort) == 0){
                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                            'port_orig'      => $originVal,
                                                            'port_dest'      => $destinyVal,
                                                            'localcharge_id' => $surchargeObj->id
                                                        ]);
                                                    }
                                                }
                                            }

                                        } else if(count(array_unique($equals_values)) > 1){ //Calculation PER_ + "Contenedor o columna" registro por contenedor
                                            $key                = null;
                                            $rows_calculations   = [];
                                            foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                $rows_calculations[$key] = [
                                                    //'type'            => $key,
                                                    'calculationtype' => $conatiner_calculation_id[$key],
                                                    'ammount'         => $conta_row[0],
                                                    'currency'        => $conta_row[1]
                                                ];
                                            }
                                            //dd($rows_calculations);
                                            $key = null;
                                            foreach($rows_calculations as $key => $row_calculation){

                                                //dd($key,$row_calculation);
                                                if($row_calculation['ammount'] != 0 || $row_calculation['ammount'] != 0.00){
                                                    //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                    $surchargeObj = null;
                                                    $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                        ->where('ammount',$row_calculation['ammount'])
                                                        ->where('currency_id',$row_calculation['currency'])
                                                        ->has($typeplace)
                                                        ->first();

                                                    if(count($surchargeObj) == 0){
                                                        $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                            'surcharge_id'       => $surchargeVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'],
                                                            'ammount'            => $row_calculation['ammount'],
                                                            'currency_id'        => $row_calculation['currency']
                                                        ]);
                                                    }

                                                    //----------------------- CARRIERS -------------------------------------------
                                                    $existsCar = null;
                                                    $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                                        ->where('localcharge_id',$surchargeObj->id)->first();
                                                    if(count($existsCar) == 0){
                                                        LocalCharCarrier::create([ // tabla localcharcarriers
                                                            'carrier_id'        => $carrierVal,
                                                            'localcharge_id'    => $surchargeObj->id
                                                        ]);
                                                    }

                                                    //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                    if($differentiatorBol){ // country
                                                        $existCount = null;
                                                        $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                            ->where('country_dest',$destinyVal)
                                                            ->where('localcharge_id',$surchargeObj->id)
                                                            ->first();
                                                        if(count($existCount) == 0){
                                                            $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                'country_orig'      => $originVal,
                                                                'country_dest'      => $destinyVal,
                                                                'localcharge_id'    => $surchargeObj->id
                                                            ]);
                                                        }
                                                    } else { // port
                                                        $existPort = null;
                                                        $existPort = LocalCharPort::where('port_orig',$originVal)
                                                            ->where('port_dest',$destinyVal)
                                                            ->where('localcharge_id',$surchargeObj->id)
                                                            ->first();
                                                        if(count($existPort) == 0){
                                                            $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                'port_orig'      => $originVal,
                                                                'port_dest'      => $destinyVal,
                                                                'localcharge_id' => $surchargeObj->id
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }                                          
                                        }

                                    } else {
                                        $currency_val   = null;
                                        $ammount        = null;
                                        $key            = null;
                                        foreach($columna_cont as $key => $conta_row){
                                            if($conta_row[3] != true){
                                                $ammount        = $conta_row[0];
                                            }
                                            $currency_val   = $conta_row[1];
                                            if($ammount != 0.00 || $ammount != null){
                                                break;
                                            }
                                        }

                                        //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                        $surchargeObj = null;
                                        $surchargeObj = LocalCharge::where('surcharge_id',$surchargeVal)
                                            ->where('typedestiny_id',$typedestinyVal)
                                            ->where('contract_id',$contract_id)
                                            ->where('calculationtype_id',$calculationtypeVal)
                                            ->where('ammount',$ammount)
                                            ->where('currency_id',$currency_val)
                                            ->has($typeplace)
                                            ->first();

                                        if(count($surchargeObj) == 0){
                                            $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id'       => $surchargeVal,
                                                'typedestiny_id'     => $typedestinyVal,
                                                'contract_id'        => $contract_id,
                                                'calculationtype_id' => $calculationtypeVal,
                                                'ammount'            => $ammount,
                                                'currency_id'        => $currency_val
                                            ]);
                                        }

                                        //----------------------- CARRIERS -------------------------------------------
                                        $existsCar = null;
                                        $existsCar = LocalCharCarrier::where('carrier_id',$carrierVal)
                                            ->where('localcharge_id',$surchargeObj->id)->first();
                                        if(count($existsCar) == 0){
                                            LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id'        => $carrierVal,
                                                'localcharge_id'    => $surchargeObj->id
                                            ]);
                                        }

                                        //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------
                                        if($differentiatorBol){ // country
                                            $existCount = null;
                                            $existCount = LocalCharCountry::where('country_orig',$originVal)
                                                ->where('country_dest',$destinyVal)
                                                ->where('localcharge_id',$surchargeObj->id)
                                                ->first();
                                            if(count($existCount) == 0){
                                                $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                    'country_orig'      => $originVal,
                                                    'country_dest'      => $destinyVal,
                                                    'localcharge_id'    => $surchargeObj->id
                                                ]);
                                            }
                                        } else { // port
                                            $existPort = null;
                                            $existPort = LocalCharPort::where('port_orig',$originVal)
                                                ->where('port_dest',$destinyVal)
                                                ->where('localcharge_id',$surchargeObj->id)
                                                ->first();
                                            if(count($existPort) == 0){
                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                    'port_orig'      => $originVal,
                                                    'port_dest'      => $destinyVal,
                                                    'localcharge_id' => $surchargeObj->id
                                                ]);
                                            }
                                        }

                                    }
                                }

                                ///////////////////////////////// END GOOD

                            } else {
                                //dd($datos_finales);
                                if($values != false){
                                    // ORIGIN -------------------------------------------------------------
                                    if($origExiBol){
                                        if($differentiatorBol == false){
                                            $originVal = Harbor::find($originVal);
                                            $originVal = $originVal->name;
                                        }else if($differentiatorBol == true){
                                            $originVal = Country::find($originVal);
                                            $originVal = $originVal['name']; 
                                        }
                                    } 
                                    // DESTINATION --------------------------------------------------------
                                    if($destiExitBol){
                                        if($differentiatorBol == false){
                                            $destinyVal = Harbor::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        }else if($differentiatorBol == true){
                                            $destinyVal = Country::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        }
                                    }
                                    //---------------------------- CALCULATION TYPE -----------------------
                                    if($calculationtypeExiBol){
                                        $calculationtypeVal = CalculationType::find($calculationtypeVal);
                                        $calculationtypeVal = $calculationtypeVal->name;
                                    }
                                    //---------------------------- TYPE - SURCHARGE -----------------------
                                    if(strnatcasecmp($chargeExc_val,$chargeVal) != 0){
                                        if($typeChargeExiBol){
                                            $surchargeVal = Surcharge::find($surchargeVal);
                                            $surchargeVal = $surchargeVal->name;
                                        }
                                    }
                                    //---------------------------- CARRIER --------------------------------
                                    if($carriExitBol){
                                        $carrierVal = Carrier::find($carrierVal);
                                        $carrierVal = $carrierVal->name;
                                    }
                                    //---------------------------- TYPE DESTINY ---------------------------
                                    if($typedestinyExitBol == true && strnatcasecmp($chargeExc_val,$chargeVal) != 0){
                                        try{
                                            $typedestinyVal = TypeDestiny::find($typedestinyVal);
                                            $typedestinyVal = $typedestinyVal->description;
                                        } catch(\Exception $e){
                                            dd($datos_finales);
                                        }
                                    }

                                    if(strnatcasecmp($chargeExc_val,$chargeVal) == 0){
                                        $twuenty_val    = 0;
                                        $forty_val      = 0;
                                        $fortyhc_val    = 0;
                                        $fortynor_val   = 0;
                                        $fortyfive_val  = 0;
                                        $currency_val   = null;
                                        $container_json = [];
                                        if($differentiatorBol == false){
                                            if($groupContainer_id != 1){ //DISTINTO A DRY
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[4] == false){                      
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin($statusCurrency,
                                                                                          $currency_bol[$key],
                                                                                          $conta_row[0],
                                                                                          $conta_row[1]); 
                                                        $container_json['C'.$key] = ''.$rspVal;
                                                    }
                                                    if($conta_row[3] != true){
                                                        if($currency_bol[$key] == false){
                                                            $currency_val   = $conta_row[1];
                                                        } else{
                                                            $currencyObj  = Currency::find($conta_row[1]);
                                                            $currency_val = $currencyObj->alphacode;
                                                        }
                                                    }
                                                }
                                                $container_json = json_encode($container_json);


                                            } else { // DRY
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[4] == false){ // columna contenedores
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin($statusCurrency,
                                                                                          $currency_bol[$key],
                                                                                          $conta_row[0],
                                                                                          $conta_row[1]); 
                                                        $container_json['C'.$key] = ''.$rspVal;
                                                    } else{ // por columna específica
                                                        if(strnatcasecmp($columns_rt_ident[$key],'twuenty') == 0){
                                                            $twuenty_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                   $currency_bol[$key],
                                                                                                   $conta_row[0],
                                                                                                   $conta_row[1]);    
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'forty') == 0){
                                                            $forty_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                 $currency_bol[$key],
                                                                                                 $conta_row[0],
                                                                                                 $conta_row[1]);
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortyhc') == 0){
                                                            $fortyhc_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                   $currency_bol[$key],
                                                                                                   $conta_row[0],
                                                                                                   $conta_row[1]);  
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortynor') == 0){
                                                            $fortynor_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                    $currency_bol[$key],
                                                                                                    $conta_row[0],
                                                                                                    $conta_row[1]);  
                                                        } else if(strnatcasecmp($columns_rt_ident[$key],'fortyfive') == 0){
                                                            $fortyfive_val = HelperAll::currencyJoin($statusCurrency,
                                                                                                     $currency_bol[$key],
                                                                                                     $conta_row[0],
                                                                                                     $conta_row[1]);
                                                        }
                                                    }  
                                                    if($conta_row[3] != true){
                                                        $currency_val = $conta_row[1];                                                        
                                                    }
                                                }
                                                $container_json = json_encode($container_json);

                                            }


                                            $exists = null;
                                            $exists = FailRate::where('origin_port',$originVal)
                                                ->where('destiny_port',$destinyVal)
                                                ->where('carrier_id',$carrierVal)
                                                ->where('contract_id',$contract_id)
                                                ->where('twuenty',$twuenty_val)
                                                ->where('forty',$forty_val)
                                                ->where('fortyhc',$fortyhc_val)
                                                ->where('fortynor',$fortynor_val)
                                                ->where('fortyfive',$fortyfive_val)
                                                ->where('containers',$container_json)
                                                ->where('currency_id',$currency_val)
                                                ->get();

                                            if(count($exists) == 0){
                                                $respFR = FailRate::create([
                                                    'origin_port'       => $originVal,
                                                    'destiny_port'      => $destinyVal,
                                                    'carrier_id'        => $carrierVal,
                                                    'contract_id'       => $contract_id,
                                                    'twuenty'           => $twuenty_val,
                                                    'forty'             => $forty_val,
                                                    'fortyhc'           => $fortyhc_val,
                                                    'fortynor'          => $fortynor_val,
                                                    'fortyfive'         => $fortyfive_val,
                                                    'containers'        => $container_json,
                                                    'currency_id'       => $currency_val
                                                ]);
                                            }
                                        }
                                    } else {
                                        if($differentiatorBol){
                                            $differentiatorVal = 2;
                                        } else {
                                            $differentiatorVal = 1;
                                        }
                                        if($calculationtypeExiBol){
                                            if(strnatcasecmp($calculation_type_exc,'PER_CONTAINER') == 0){
                                                $equals_values = [];
                                                $key = null;
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[3] == true && $conta_row[2] != true){
                                                        array_push($equals_values,$conta_row[0]);
                                                    } else if($conta_row[3] == false){
                                                        array_push($equals_values,$conta_row[0]); 
                                                    }
                                                }

                                                if(count(array_unique($equals_values)) == 1){ // Valores iguales.
                                                    $currency_val   = null;
                                                    $ammount        = null;
                                                    $key            = null;
                                                    $currency_bol_f = true;
                                                    foreach($columna_cont as $key => $conta_row){
                                                        if($conta_row[3] != true ){
                                                            $ammount        = $conta_row[0];
                                                        }
                                                        if($variant_currency){                          
                                                            $currency_val   = $conta_row[1];
                                                            break;
                                                        } else {
                                                            if($currency_bol[$key] == false){
                                                                $currency_bol_f = false;
                                                                $currency_val   = $conta_row[1];
                                                            }
                                                        }
                                                    }

                                                    $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                       $currency_bol_f,
                                                                                       $ammount,
                                                                                       $currency_val);
                                                    if($currency_bol_f){
                                                        $currencyObj  = Currency::find($currency_val);
                                                        $currency_val = $currencyObj->alphacode;
                                                    }

                                                    //dd($ammount,$currency_val);
                                                    $exists = null;
                                                    $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$calculationtypeVal)
                                                        ->where('ammount',$ammount)
                                                        ->where('currency_id',$currency_val)
                                                        ->where('carrier_id',$carrierVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();
                                                    if(count($exists) == 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $calculationtypeVal,  //////
                                                            'ammount'            => $ammount, //////
                                                            'currency_id'        => $currency_val, //////
                                                            'carrier_id'         => $carrierVal,
                                                            'differentiator'     => $differentiatorVal
                                                        ]);
                                                    }
                                                } else if(count(array_unique($equals_values)) > 1){ //Valores distintos
                                                    $key                 = null;
                                                    $rows_calculations   = [];
                                                    foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                        $calculationtypeVal = CalculationType::find($conatiner_calculation_id[$key]);
                                                        $calculationtypeVal = $calculationtypeVal->name;
                                                        if($currency_bol[$key]){
                                                            $currency_val = Currency::find($conta_row[1]);
                                                            $currency_val = $currency_val->alphacode;
                                                        } else {
                                                            $currency_val = $conta_row[1];
                                                        }
                                                        $ammount = null;
                                                        $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                           $currency_bol[$key],
                                                                                           $conta_row[0],
                                                                                           $conta_row[1]);
                                                        $ammoun_zero = false;
                                                        if($conta_row[0] == 0.0 || $conta_row[0] == 0){
                                                            $ammoun_zero = true;
                                                        }
                                                        $rows_calculations[$key] = [
                                                            'calculationtype' => $calculationtypeVal,
                                                            'ammount'         => $ammount,
                                                            'ammount_zero'    => $ammoun_zero,
                                                            'currency'        => $currency_val
                                                        ];
                                                    }
                                                    //dd($rows_calculations);
                                                    foreach($rows_calculations as $key => $row_calculation){
                                                        if($row_calculation['ammount_zero'] != true){
                                                            $exists = null;
                                                            $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                                ->where('port_orig',$originVal)
                                                                ->where('port_dest',$destinyVal)
                                                                ->where('typedestiny_id',$typedestinyVal)
                                                                ->where('contract_id',$contract_id)
                                                                ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                                ->where('ammount',$row_calculation['ammount'])
                                                                ->where('currency_id',$row_calculation['currency'])
                                                                ->where('carrier_id',$carrierVal)
                                                                ->where('differentiator',$differentiatorVal)
                                                                ->get();
                                                            if(count($exists) == 0){
                                                                FailSurCharge::create([
                                                                    'surcharge_id'       => $surchargeVal,
                                                                    'port_orig'          => $originVal,
                                                                    'port_dest'          => $destinyVal,
                                                                    'typedestiny_id'     => $typedestinyVal,
                                                                    'contract_id'        => $contract_id,
                                                                    'calculationtype_id' => $row_calculation['calculationtype'],  //////
                                                                    'ammount'            => $row_calculation['ammount'], //////
                                                                    'currency_id'        => $row_calculation['currency'], //////
                                                                    'carrier_id'         => $carrierVal,
                                                                    'differentiator'     => $differentiatorVal
                                                                ]);
                                                            }

                                                        }
                                                    }
                                                }

                                            } else {

                                                $ammount        = null;
                                                $key            = null;
                                                foreach($columna_cont as $key => $conta_row){
                                                    if($conta_row[3] != true && $conta_row[0] != 0.00 && $conta_row[0] != null){
                                                        $ammount        = $conta_row[0];
                                                        break;
                                                    }
                                                }
                                                $key            = null;
                                                $currency_val   = null;
                                                $currency_bol_f = true;
                                                foreach($columna_cont as $key => $conta_rowT){
                                                    if($variant_currency){                          
                                                        $currency_val   = $conta_rowT[1];
                                                        break;
                                                    } else {
                                                        if($currency_bol[$key] == false){
                                                            $currency_bol_f = false;
                                                            $currency_val   = $conta_rowT[1];
                                                        }
                                                    }
                                                }


                                                $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                   $currency_bol_f,
                                                                                   $ammount,
                                                                                   $currency_val);
                                                if($currency_bol_f){
                                                    $currencyObj  = Currency::find($currency_val);
                                                    $currency_val = $currencyObj->alphacode;
                                                }
                                                //dd('registro pr ship',$variant_currency,$columna_cont,$currency_val,$ammount);
                                                $exists = null;
                                                $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                    ->where('port_orig',$originVal)
                                                    ->where('port_dest',$destinyVal)
                                                    ->where('typedestiny_id',$typedestinyVal)
                                                    ->where('contract_id',$contract_id)
                                                    ->where('calculationtype_id',$calculationtypeVal)
                                                    ->where('ammount',$ammount)
                                                    ->where('currency_id',$currency_val)
                                                    ->where('carrier_id',$carrierVal)
                                                    ->where('differentiator',$differentiatorVal)
                                                    ->get();
                                                if(count($exists) == 0){
                                                    FailSurCharge::create([
                                                        'surcharge_id'       => $surchargeVal,
                                                        'port_orig'          => $originVal,
                                                        'port_dest'          => $destinyVal,
                                                        'typedestiny_id'     => $typedestinyVal,
                                                        'contract_id'        => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal,  //////
                                                        'ammount'            => $ammount, //////
                                                        'currency_id'        => $currency_val, //////
                                                        'carrier_id'         => $carrierVal,
                                                        'differentiator'     => $differentiatorVal
                                                    ]);
                                                }
                                            }
                                        } else {// Calculation Type desconocido

                                            $key                 = null;
                                            $rows_calculations   = [];
                                            foreach($columna_cont as $key => $conta_row){// Cargamos cada columna para despues insertarlas en la BD
                                                $calculationtypeValFail = null;
                                                $calculationtypeValFail = $key.' '.$calculationtypeVal;
                                                if($currency_bol[$key]){
                                                    $currency_val = Currency::find($conta_row[1]);
                                                    $currency_val = $currency_val->alphacode;
                                                } else {
                                                    $currency_val = $conta_row[1];
                                                }
                                                $ammount = null;
                                                $ammount = HelperAll::currencyJoin($statusCurrency,
                                                                                   $currency_bol[$key],
                                                                                   $conta_row[0],
                                                                                   $conta_row[1]);
                                                $ammoun_zero = false;
                                                if($conta_row[0] == 0.0 || $conta_row[0] == 0){
                                                    $ammoun_zero = true;
                                                }
                                                $rows_calculations[$key] = [
                                                    'calculationtype' => $calculationtypeValFail,
                                                    'ammount'         => $ammount,
                                                    'ammount_zero'    => $ammoun_zero,
                                                    'currency'        => $currency_val
                                                ];
                                            }
                                            //dd('llega aqui Cals',$rows_calculations);
                                            foreach($rows_calculations as $key => $row_calculation){
                                                if($row_calculation['ammount_zero'] != true){
                                                    $exists = null;
                                                    $exists = FailSurCharge::where('surcharge_id',$surchargeVal)
                                                        ->where('port_orig',$originVal)
                                                        ->where('port_dest',$destinyVal)
                                                        ->where('typedestiny_id',$typedestinyVal)
                                                        ->where('contract_id',$contract_id)
                                                        ->where('calculationtype_id',$row_calculation['calculationtype'])
                                                        ->where('ammount',$row_calculation['ammount'])
                                                        ->where('currency_id',$row_calculation['currency'])
                                                        ->where('carrier_id',$carrierVal)
                                                        ->where('differentiator',$differentiatorVal)
                                                        ->get();
                                                    if(count($exists) == 0){
                                                        FailSurCharge::create([
                                                            'surcharge_id'       => $surchargeVal,
                                                            'port_orig'          => $originVal,
                                                            'port_dest'          => $destinyVal,
                                                            'typedestiny_id'     => $typedestinyVal,
                                                            'contract_id'        => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'],  //////
                                                            'ammount'            => $row_calculation['ammount'], //////
                                                            'currency_id'        => $row_calculation['currency'], //////
                                                            'carrier_id'         => $carrierVal,
                                                            'differentiator'     => $differentiatorVal
                                                        ]);
                                                    }

                                                }
                                            }  
                                            //dd('registro');
                                        }
                                    }
                                }

                            }
                            // ELSE O FIN DEL IF PARA FALLIDOS O BUENOS

                            /////////////////////////////////

                        }
                    }
                }
                $countRow++;
            }

            $nopalicaHs = Harbor::where('name','No Aplica')->get();
            $nopalicaCs = Country::where('name','No Aplica')->get();
            foreach($nopalicaHs as $nopalicaH){
                $nopalicaH = $nopalicaH['id'];
            }
            foreach($nopalicaCs as $nopalicaC){
                $nopalicaC = $nopalicaC['id'];
            }

            $failsurchargeS = FailSurCharge::where('contract_id','=',$this->contract_id)->where('port_orig','LIKE','%No Aplica%')->delete();
            $failsurchargeS = FailSurCharge::where('contract_id','=',$this->contract_id)->where('port_dest','LIKE','%No Aplica%')->delete();

            $surchargecollection = LocalCharge::where('contract_id',$this->contract_id)
                ->whereHas('localcharcountries',function($query) use($nopalicaC){
                    $query->where('country_dest',$nopalicaC)->orWhere('country_orig',$nopalicaC);
                })
                ->orWhereHas('localcharports',function($q) use($nopalicaH){
                    $q->where('port_dest','=',$nopalicaH)->orWhere('port_orig',$nopalicaH);
                })->forceDelete();

            // dd($collection);

            $userNotifique = User::find($this->user_id);
            $message = 'The file imported was processed :' .$this->contract_id ;
            $userNotifique->notify(new SlackNotification($message));
            $userNotifique->notify(new N_general($userNotifique,$message)); 
        } else {
            //imprimir en el log error
            Log::error('Container calculation type relationship error');
        }

    }
}