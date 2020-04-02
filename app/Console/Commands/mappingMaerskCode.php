<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Harbor;
use App\Country;

class mappingMaerskCode extends Command
{
    protected $vcodes = [
        'USQBR', 
        'GDSTG', 
        'JPSTS', 
        'KROKP', 
        'YEMKX', 
        'INNSA', 
        'CNDJG', 
        'VNPHG',
        'IQKAZ', 
        'ZAJBN',
        'CNSGU',
        'USORL',
        'EGHBE'
    ];

    protected $vjson = [
        'USQBR' => [ "USBRO", "3AT772T03EQ3G" ],
        'GDSTG' => [ "GDSGO", "2PG78B5F6R0QN" ],
        'JPSTS' => [ "JPSSS", "1QHCULQT67QGH" ], 
        'KROKP' => [ "KROKP", "0TKSQLQ5JUF0F" ], 
        'YEMKX' => [ "YEAMU", "2VGY9AJ6PYB9B" ], 
        'INNSA' => [ "INJHT", "20JS07ETK8AE1" ], 
        'CNDJG' => [ "CNDOJ", "BIMUZC5Y5KWY4" ],  
        'VNPHG' => [ "VNSGI", "6KGD8QFNMV2D1" ],
        'IQKAZ' => [ "IQZU1", "3HQBNCXJSNPV7" ], 
        'ZAJBN' => [ "ZAJNB", "3IGU998UTZS73" ],
        'CNSGU' => [ "CNSGY", "2LCVSUBQ1PPXG" ],
        'USORL' => [ "USORL", "1BDJLNZKFEQV3" ],
        'EGHBE' => [ "EGBEA", "1MNUANWGGZIQU" ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:mappingMaerskCode';

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

    public function updatePortManually($port)
    {
        $port->api_varation = json_encode(['maersk' => $this->vjson[$port->code], "CMA-CMG" => [""]]);

        $country = Country::where('code', substr($this->vjson[$port->code][0], 0, 2))->get()->first();
        $port->country_id = $country->id;

        $port->update();

    }

    public function updatePortVariation($port, $location, $port_name)
    {
        if(!in_array($port->code, ['ALL', 'NoAp']))
        {
            $api_varation = array();
            $api_varation = [
                "maersk" => [ 
                    $location['maerskRkstCode'], 
                    $location['maerskGeoLocationId']
                ],
                "CMA-CMG" => [""]
            ];

            $port->api_varation = json_encode($api_varation);

            if(!$this->hasValidCountry($port)){
                $country = Country::where('code', $location['countryCode'])->get()->first();
                $port->country_id = $country->id;
            }
                
            $port->update();

            echo 'Port name: '.$port_name."\r\n"; 
            echo 'Port: '.$port->name.' '.$port->country->name;            
            echo ' Counts: 1 '.$location['maerskGeoLocationId'].' '.$location['maerskRkstCode']."\r\n";                                         
            echo "------------------------------\r\n";
        }

    }

    public function filterByExplode($collection, $arr_port_name, $key_number)
    {
        $filtered = $collection->filter(function ($value, $key) use ($arr_port_name, $key_number) {
                $str = trim(strtolower(str_replace(',', '', $arr_port_name[$key_number])));
                
                if(!empty($str))
                    return strpos(strtolower($value['cityName']), $str) !== false;
            });

        return $filtered;
    }

    public function filterByUnLocCode($collection, $port)
    {
        $filtered = $collection->filter(function ($value, $key) use ($port){
                return isset($value['unLocCode']) && $value['unLocCode'] == $port->code;
            });

        return $filtered;
    }

    public function filterByRkstCode($collection, $port)
    {
        $filtered = $collection->filter(function ($value, $key) use ($port){
                return isset($value['maerskRkstCode']) && $value['maerskRkstCode'] == $port->code;
            });

        return $filtered;
    }

    public function makeRequest($uri){
        // Call API Maersk filtered by cityName and Country Code
        $ch = curl_init($uri);                                                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);                                                         
        curl_close($ch);

        // Decoding json locations results
        return json_decode($json, true);
    }

    public function hasValidCountry($port){
        return !in_array($port->country->code, ['No Aplica', 'ALL', 'PPP']);
    }

    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $harbors = Harbor::all();

        foreach($harbors as $port)
        {                                                 
            
            if(in_array($port->code, $this->vcodes)){
                $this->updatePortManually($port);
            } else {

                // Separate name by space
                $arr_port_name = explode(' ',$port->name);  

                if(count($arr_port_name)>1)
                    $port_name = $arr_port_name[0];
                else 
                    $port_name = $port->name;                                                   

                $url = 'https://api.maersk.com/locations/?brand=maeu&cityName='.$port_name.'&type=city&pageSize=100&sort=cityName';

                if($this->hasValidCountry($port)){
                    $url .= '&countryCode='.$port->country->code;
                }

                $locations = $this->makeRequest($url);

                // Check if status results is different to 404
                if(!isset($locations['status'])){

                    $locations = collect($locations);

                    if($locations->count() == 1){

                        $location = $locations->first();

                        echo "Zero Level \r\n";

                        $this->updatePortVariation($port, $location, $port_name);

                    } 
                    else if($locations->count() > 1) {

                        if(count($arr_port_name) == 1){

                            $vlocations = $this->filterByUnLocCode($locations, $port);

                            $location = $vlocations->first();

                            echo "First Level \r\n";

                            $this->updatePortVariation($port, $location, $port_name);

                        } else {

                            $filtered = $this->filterByExplode($locations, $arr_port_name, 1);

                            if($filtered->count() == 1) {

                                $location = $filtered->first();

                                echo "Second Level \r\n";

                                $this->updatePortVariation($port, $location, $port_name);

                            } else if($filtered->count() > 1) {

                                $deepLevel = $this->filterByUnLocCode($filtered, $port);

                                if($deepLevel->count() == 1){

                                    $location = $deepLevel->first();

                                    echo "Third Level \r\n";
                                    
                                    $this->updatePortVariation($port, $location, $port_name);
                                    
                                } else if($deepLevel->count() > 1){

                                    $deeperLevel = $deepLevel;

                                    if(count($arr_port_name) > 2)
                                        $deeperLevel = $this->filterByExplode($filtered, $arr_port_name, 2);

                                    if($deeperLevel->count() == 1){

                                        $location = $deeperLevel->first();

                                        echo "Fourth Level \r\n";

                                        $this->updatePortVariation($port, $location, $port_name);

                                        

                                    } else if($deeperLevel->count() > 1){

                                        $deepestLevel = $this->filterByRkstCode($deeperLevel, $port);

                                        if($deepestLevel->count() == 1){

                                            $location = $deepestLevel->first();

                                            echo "Fifth Level \r\n";
                                            $this->updatePortVariation($port, $location, $port_name);
     
                                            

                                        } else {
                                            echo "Aqui en deepest Level se pasó \r\n";
                                        }
                                    } else {
                                        $url = 'https://api.maersk.com/locations/?brand=maeu&unLocCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                                        $locations = collect($locations);
                                
                                        $locations = $this->makeRequest($url);

                                        // Check if status results is different to 404
                                        if(!isset($locations['status'])){

                                            $location = $locations->first();

                                            echo "Fourteen Level \r\n";
                                            $this->updatePortVariation($port, $location, $port_name);

                                        } else {
                                            $url = 'https://api.maersk.com/locations/?brand=maeu&maerskRkstCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                                            $locations = $this->makeRequest($url);

                                            if(!isset($locations['status'])){

                                                $location = $locations->first();

                                                echo "Fiftheen Level \r\n";
                                                $this->updatePortVariation($port, $location, $port_name);

                                            } else {
                                                echo 'NOT FOUND 5';
                                                echo 'Port: '.$port->name.' '.$port->country->name;            
                                                echo " Counts: NOT FOUND \r\n";                                         
                                                echo "------------------------------\r\n";
                                            }

                                        }
                                    }

                                } else {
                                    $url = 'https://api.maersk.com/locations/?brand=maeu&unLocCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';
                                
                                    $locations = $this->makeRequest($url);

                                    // Check if status results is different to 404
                                    if(!isset($locations['status'])){
                                        $locations = collect($locations);

                                        $location = $locations->first();

                                        echo "Twelve Level \r\n";
                                        $this->updatePortVariation($port, $location, $port_name);

                                    } else {
                                        $url = 'https://api.maersk.com/locations/?brand=maeu&maerskRkstCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                                        $locations = $this->makeRequest($url);

                                        if(!isset($locations['status'])){
                                            $locations = collect($locations);

                                            $location = $locations->first();

                                            echo "Thirteen Level \r\n";
                                            $this->updatePortVariation($port, $location, $port_name);

                                        } else {
                                            echo 'NOT FOUND 4';
                                            echo 'Port: '.$port->name.' '.$port->country->name;            
                                            echo " Counts: NOT FOUND \r\n";                                         
                                            echo "------------------------------\r\n";
                                        }

                                    }
                                }

                            } else {
                                $url = 'https://api.maersk.com/locations/?brand=maeu&unLocCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';
                                
                                $locations = $this->makeRequest($url);

                                // Check if status results is different to 404
                                if(!isset($locations['status'])){
                                    $locations = collect($locations);

                                    $location = $locations->first();

                                    echo "Ten Level \r\n";
                                    $this->updatePortVariation($port, $location, $port_name);

                                } else {
                                    $url = 'https://api.maersk.com/locations/?brand=maeu&maerskRkstCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                                    $locations = $this->makeRequest($url);

                                    if(!isset($locations['status'])){

                                        $locations = collect($locations);

                                        $location = $locations->first();

                                        echo "Eleven Level \r\n";
                                        $this->updatePortVariation($port, $location, $port_name);

                                    } else {
                                        echo 'NOT FOUND 3';
                                        echo 'Port: '.$port->name.' '.$port->country->name;            
                                        echo " Counts: NOT FOUND \r\n";                                         
                                        echo "------------------------------\r\n";
                                    }

                                }

                            }

                            
                        }
                    } else {
                        $url = 'https://api.maersk.com/locations/?brand=maeu&unLocCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';
                                
                        $locations = $this->makeRequest($url);

                        // Check if status results is different to 404
                        if(!isset($locations['status'])){
                            $locations = collect($locations);

                            $location = $locations->first();

                            echo "eigth Level \r\n";
                            $this->updatePortVariation($port, $location, $port_name);

                        } else {
                            $url = 'https://api.maersk.com/locations/?brand=maeu&maerskRkstCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                            $locations = $this->makeRequest($url);

                            if(!isset($locations['status'])){

                                $locations = collect($locations);

                                $location = $locations->first();

                                echo "Nineth Level \r\n";
                                $this->updatePortVariation($port, $location, $port_name);

                            } else {
                                echo 'NOT FOUND 2';
                                echo 'Port: '.$port->name.' '.$port->country->name;            
                                echo " Counts: NOT FOUND \r\n";                                         
                                echo "------------------------------\r\n";
                            }

                        }
                    }
                } else {

                    $url = 'https://api.maersk.com/locations/?brand=maeu&unLocCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';
                    
                    $locations = $this->makeRequest($url);

                    // Check if status results is different to 404
                    if(!isset($locations['status'])){
                        $locations = collect($locations);

                        $location = $locations->first();

                        echo "Sixth Level \r\n";
                        $this->updatePortVariation($port, $location, $port_name);

                    } else {
                        $url = 'https://api.maersk.com/locations/?brand=maeu&maerskRkstCode='.$port->code.'&countryCode='.$port->country->code.'&type=city&pageSize=100&sort=cityName';

                        $locations = $this->makeRequest($url);

                        if(!isset($locations['status'])){
                            $locations = collect($locations);

                            $location = $locations->first();

                            echo "Seventh Level \r\n";
                            $this->updatePortVariation($port, $location, $port_name);

                        } else {
                            echo 'NOT FOUND 1';
                            echo 'Port: '.$port->name.' '.$port->country->name;            
                            echo " Counts: NOT FOUND \r\n";                                         
                            echo "------------------------------\r\n";
                        }

                    }
                }
            }
                                                                                          
        }
    }
}
