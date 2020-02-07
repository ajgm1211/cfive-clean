<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MappingLocation;

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

            // Initialize CURL:
            $ch = curl_init('https://api.maersk.com/locations/?type=city&pageSize=100&sort=cityName');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $locations = json_decode($json, true);
            
            MappingLocation::truncate();
            
            foreach($locations as $key=>$value){
                $array = array();
                $array=["maersk"=>[$value['maerskRkstCode'],$value['maerskGeoLocationId']],"CMA-CMG"=>[""]];
                $mapping = new MappingLocation();
                $mapping->city_name = $value['cityName'];
                $mapping->variation = json_encode($array);
                $mapping->save();                
            }

        } catch(\Exception $e){
            return $this->info($e->getMessage());
        }

        $this->info('Command Mapping Maerks Locations executed successfully!');
    }
}
