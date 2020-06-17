<?php

namespace App\Http\Controllers;

use HelperAll;
use PrvHarbor;
use PrvCarrier;
use App\Harbor;
use App\Carrier;
use App\TransitTime;
use App\TransitTimeFail;
use App\DestinationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ImportationTransitTimeController extends Controller
{

    public function index()
    {
        return view('ImportationTransitime.index');
    }

    public function storeMedia(Request $request){
        $path = storage_path('app/public');

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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $failsTT = TransitTimeFail::where('via','!=',null)->orWhere('via',null)->delete();

        $file 				= $request->input('document');
        if(!empty($file)){
            $load           = null;
            $inputFileType  = null;

            $extObj = new \SplFileInfo($file);
            $ext    = $extObj->getExtension();
            if(strnatcasecmp($ext,'xlsx')==0){
                $inputFileType = 'Xlsx';
            } else if(strnatcasecmp($ext,'xls')==0){
                $inputFileType = 'Xls';
            } else {
                $inputFileType = 'Csv';
            }
            $reader = IOFactory::createReader($inputFileType);
            $spreadsheet    = $reader->load(Storage::disk('local2')->url($file));
            $sheetData      = $spreadsheet->getActiveSheet()->toArray();
            //dd($sheetData);
            $origin     = 'ORIGIN';
            $detiny     = 'DESTINY';
            $carrier    = 'CARRIER';
            $time       = 'T\T';
            $type       = 'DESTINATION TYPE';
            $via        = 'VIA';
            $columnsSelected = [$origin,$detiny,$carrier,$time,$type,$via];

            $final_columns = collect([]);
            foreach($columnsSelected as $columnSelect){
                foreach($sheetData[0] as $key => $cells){
                    //dd($key,$cells);
                    if($columnSelect ==  $cells){
                        $final_columns->put($cells,$key);
                    }

                }
            }

            //dd($final_columns['ORIGIN']);
            $fila = 1;
            foreach($sheetData as $row){
                $origin_val     = null;
                $destiny_val    = null;
                $carrier_val    = null;
                $time_val       = null;
                $type_val       = null;
                $via_val        = null;

                $type_bol       = false;

                if($fila > 1){
                    $origin_val     = trim($row[$final_columns[$origin]]);
                    $destiny_val    = trim($row[$final_columns[$detiny]]);
                    $carrier_val    = trim($row[$final_columns[$carrier]]);
                    $time_val       = trim($row[$final_columns[$time]]);
                    $type_val       = trim($row[$final_columns[$type]]);
                    $via_val        = trim($row[$final_columns[$via]]);

                    $carrierArr     = PrvCarrier::get_carrier($carrier_val);
                    $carri_Bol      = $carrierArr['boolean'];
                    $carrier_val    = $carrierArr['carrier'];

                    $origin_arr     = PrvHarbor::get_harbor_simple($origin_val);
                    $origin_Bol     = $origin_arr['boolean'];
                    $origin_val     = $origin_arr['puerto'];

                    $destiny_arr     = PrvHarbor::get_harbor_simple($destiny_val);
                    $destiny_Bol     = $destiny_arr['boolean'];
                    $destiny_val     = $destiny_arr['puerto'];

                    $destinationTObj = DestinationType::where('code',$type_val)->first();
                    if(count($destinationTObj) == 1 ){
                        $type_bol = true;
                        $type_val = $destinationTObj->id;
                        if($type_val == 1){
                            $via_val = ' ';
                        }
                    } elseif(count($place_val) == 0){
                        $type_val = $via_val.'(Error)';
                    } 

                    if($carri_Bol == true && $origin_Bol == true && $destiny_Bol == true && $type_bol == true && (int)$time_val != 0 ){

                        $transitTime = TransitTime::where('origin_id',$origin_val)
                            ->where('destination_id',$destiny_val)
                            ->where('carrier_id',$carrier_val)
                            //->where('service_id',$type_val)
                            ->get();
                        if(count($transitTime) == 0){
                            $transitTime = new TransitTime();
                            $transitTime->origin_id         = $origin_val;
                            $transitTime->destination_id    = $destiny_val;
                            $transitTime->carrier_id        = $carrier_val;
                            $transitTime->service_id        = $type_val;
                            $transitTime->transit_time      = $time_val;
                            $transitTime->via               = $via_val;
                            $transitTime->save();
                        } elseif(count($transitTime) == 1) {
                            $transitTime[0]->origin_id         = $origin_val;
                            $transitTime[0]->destination_id    = $destiny_val;
                            $transitTime[0]->carrier_id        = $carrier_val;
                            $transitTime[0]->service_id        = $type_val;
                            $transitTime[0]->transit_time      = $time_val;
                            $transitTime[0]->via               = $via_val;
                            $transitTime[0]->update();
                        }
                    } else {
                        if((int)$time_val != 0){
                            if($carri_Bol){
                                $carrier_val = Carrier::find($carrier_val);
                                $carrier_val = $carrier_val->name;
                            }

                            if($origin_Bol){
                                $origin_val = Harbor::find($origin_val);
                                $origin_val = $origin_val->name;
                            }

                            if($destiny_Bol){
                                $destiny_val = Harbor::find($destiny_val);
                                $destiny_val = $destiny_val->name;
                            }

                            if($type_bol){
                                $type_val = DestinationType::find($type_val);
                                $type_val = $type_val->name;
                            }

                            $transitTimeFail                    = new TransitTimeFail();
                            $transitTimeFail->origin            = $origin_val;
                            $transitTimeFail->destiny           = $destiny_val;
                            $transitTimeFail->carrier           = $carrier_val;
                            $transitTimeFail->destination_type  = $type_val;
                            $transitTimeFail->transit_time      = (int)$time_val;
                            $transitTimeFail->via               = $via_val;
                            $transitTimeFail->save();
                        }
                    }
                }
                $fila = $fila + 1;
            }
        }
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
}
