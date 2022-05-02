<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Province;
use App\InlandDistance;


class importLocationsFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importLocationsFromExcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command allows to import  locations from an Excel document into the connected Database. A few considerations: 
                                - Upload your file into storage/app/public/pdf 
                                - This command uses the old locations structure
                                - Provinces are optional, but if you choose to import provinces, they will be compared by name.
                                - Distances will be considered in Kilometers
                                - ZIPs are optional. If you dont want to import them include the header, but leave the fields blank
                                - One Port at a time!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $expected_headers = ["Location", "Zip", "Distance", "Province"];
        
        $filename = $this->ask('Insert the file name (with extension)');
        $country_id = $this->ask('Insert the country ID');
        $harbor_id = $this->ask('Insert the Port ID');

        $file = Storage::disk('pdf')->url($filename);

        $input_file_type = $this->validateFileExtension($filename, $file);

        $sheet = $this->parseFile($file, $input_file_type);

        $sheet_data = $this->validateHeaders($sheet, $expected_headers);

        $location_data = $this->extractLocationData($sheet_data, $sheet, $expected_headers);

        $this->createLocations($location_data, $country_id, $harbor_id);
      
    }

    public function validateFileExtension($filename, $file)
    {
        $file_info = new \SplFileInfo($filename);
        $file_extension = $file_info->getExtension();

        if (strnatcasecmp($file_extension, 'xlsx') == 0) {
            $input_file_type = 'Xlsx';
        } elseif (strnatcasecmp($file_extension, 'xls') == 0) {
            $input_file_type = 'Xls';
        } else {
            $this->error("Unsupported file type");
            return;
        }

        return $input_file_type;
    }

    public function parseFile($file, $input_file_type)
    {
        $reader = IOFactory::createReader($input_file_type);
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($file)->getActiveSheet();
        
        return $sheet;
    }

    public function validateHeaders($sheet, $expected_headers)
    {
        $sheet_data = [];

        $sheet_data['highest_row'] = $sheet->getHighestRow(); 
        $sheet_data['highest_column'] = $sheet->getHighestColumn();

        $sheet_data['headers'] = $sheet->rangeToArray('A1:' . $sheet_data['highest_column'] . 1, NULL, TRUE, FALSE, TRUE)[1];

        foreach($expected_headers as $header){
            if(!in_array($header, $sheet_data['headers'])){
                $this->error("Sorry, " . $header . " not found in file headers. Required headers are Location, Zip, Distance and Province");
                return;
            }
        }

        return $sheet_data;
    }

    public function extractLocationData($sheet_data, $sheet, $expected_headers)
    {
        $final_array = [];
    
        for ($row = 2; $row <= $sheet_data['highest_row']; $row++) {
             
            $row_data = $sheet->rangeToArray('A' . $row . ':' . $sheet_data['highest_column'] . $row, NULL, TRUE, FALSE, TRUE);
            
            $location_array = [];
            
            for ($column = 'A'; $column <= $sheet_data['highest_column']; ++$column) {
                if (!empty($row_data[$row][$column]) && in_array($sheet_data['headers'][$column],$expected_headers)) {
                    $location_array[$sheet_data['headers'][$column]] = $row_data[$row][$column];
                }
            }

            array_push($final_array, $location_array);
        }

        return $final_array;
    }

    public function createLocations($location_data, $country_id, $harbor_id)
    {
        
        foreach($location_data as $location_array){
            if(isset($location_array['Location'])){

                $display_name = $location_array['Location'];
    
                if(isset($location_array['Province'])){
                    $province = Province::where('name',$location_array['Province'])->first();
    
                    if(is_null($province)){
                        $province = Province::create([
                            'name' => $location_array['Province'],
                            'country_id' => $country_id
                        ]);
                    }
    
                    $display_name = $display_name . ", " . $location_array['Province'];
                }
    
                if(isset($location_array['Zip'])){
                    $zip = $location_array['Zip'];
                    $display_name = $location_array['Zip'] . ", " . $display_name;
                }else{
                    $zip = '';
                }
    
    
                $location = InlandDistance::create([
                    'address' => $location_array['Location'],
                    'zip' => !empty($zip) ? $zip : null,
                    'distance' => isset($location_array['Distance']) ? $location_array['Distance'] : 0,
                    'display_name' => $display_name,
                    'harbor_id' => $harbor_id,
                    'province_id' => isset($province) ? $province->id : null
                ]);
    
            }
        }
    }
}
