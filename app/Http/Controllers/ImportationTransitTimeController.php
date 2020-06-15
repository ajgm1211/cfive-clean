<?php

namespace App\Http\Controllers;

use PrvHarbor;
use PrvCarrier;
use App\Harbor;
use App\Carrier;
use App\TransitTime;
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
            $time       = 'DAYS';
            $type       = 'Schedule Type';
            $columnsSelected = [$origin,$detiny,$carrier,$time,$type];

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
                if($fila > 1){
                    $origin_val     = $row[$final_columns[$origin]];
                    $destiny_val    = $row[$final_columns[$detiny]];
                    $carrier_val    = $row[$final_columns[$carrier]];
                    $time_val       = $row[$final_columns[$time]];
                    $type_val       = $row[$final_columns[$type]];

                    $carrierArr     = PrvCarrier::get_carrier($carrier_val);
                    $carriExitBol   = $carrierArr['boolean'];
                    $carrier_val    = $carrierArr['carrier'];
                    
                    $origin_arr     = PrvHarbor::get_harbor_simple($origin_val);
                    $originExitBol  = $origin_arr['boolean'];
                    $origin_val     = $origin_arr['puerto'];
                    
                    $destiny_arr     = PrvHarbor::get_harbor_simple($destiny_val);
                    $destinyExitBol  = $destiny_arr['boolean'];
                    $destiny_val     = $destiny_arr['puerto'];
                    
                    dd($origin_val,$destiny_val,$carrier_val);
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
