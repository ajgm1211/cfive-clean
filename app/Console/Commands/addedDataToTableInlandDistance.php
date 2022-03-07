<?php

namespace App\Console\Commands;

use App\DistanceKmLocation;
use App\InlandDistance;
use App\InlandProvince;
use App\Location;
use Illuminate\Console\Command;

class addedDataToTableInlandDistance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addedDataToTableInlandDistance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            
            $locationOld=InlandDistance::get()->map(function ($locations){
                return $locations->only(['address','distance','harbor_id','province']);
            });

            foreach($locationOld as $location){
                $newLocation=Location::where('name',$location['address'])->first();
                DistanceKmLocation::updateOrCreate(
                ['distance'=> $location['distance'],'location_id'=>$newLocation->id,'harbors_id'=>$location['harbor_id']],
                [
                    'distance'=> $location['distance'],
                    'location_id'=>$newLocation->id,
                    'harbors_id'=>$location['harbor_id']
                ]);

            }
            \Log::info('done distance');
        }catch(\Exception $e) {
            \Log::info($e->getMessage());
        }
        
    }
}
