<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MappingLocation;
use App\Harbor;

class MappingHarborCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:mappinglocations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mapping Maerks Locations';

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
        try{

            MappingLocation::truncate();

            $array_a = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            $array_b = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

            foreach($array_a as $a){
                foreach($array_b as $b){
                    // Initialize CURL:
                    $ch = curl_init('https://api.maersk.com/locations/?brand=maeu&cityName='.$a.$b.'&type=city&pageSize=100&sort=cityName');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Store the data:
                    $json = curl_exec($ch);
                    curl_close($ch);

                    // Decode JSON response:
                    $locations = json_decode($json, true);

                    foreach($locations as $key=>$value){
                        if(!empty($value['maerskRkstCode']) && !empty($value['cityName'])){
                            $array = array();
                            $array=["maersk"=>[$value['maerskRkstCode'],$value['maerskGeoLocationId']],"CMA-CMG"=>[""]];
                            $mapping = new MappingLocation();
                            $mapping->city_name = $value['cityName'];
                            $mapping->variation = json_encode($array);
                            $mapping->save();

                            $harbor = Harbor::where('name',$value['cityName'])->first();
                            if(!empty($harbor)){
                                $harbor->api_varation = json_encode($array);
                                $harbor->update();   
                            }
                        }
                    }
                }
            }
        } catch(\Exception $e){
            return $this->info($e->getMessage());
        }

        $this->info('Command Mapping Maerks Locations executed successfully!');
    }
}
