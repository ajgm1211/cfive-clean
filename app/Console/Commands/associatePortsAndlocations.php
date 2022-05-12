<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Location;
use App\Harbor;
use App\DistanceKmLocation;
use App\HarborsLocationSearch;

class associatePortsAndlocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:associatePortsAndlocations';

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
            $harbors=Harbor::where('country_id',66)->get();
            $locations=Location::with('province')->get();

        foreach($locations as $location){
            foreach($harbors as $harbor){
                if($location['province']['country_id']==66){
                    HarborsLocationSearch::updateOrCreate(
                        ['location_id'=>$location['id'],'harbor_id'=>$harbor['id']],
                        [
                            'harbor_id'=>$harbor['id'],
                            'location_id'=>$location['id']
                        ]);
                }
            }
        }
            \Log::info('done asociate locations');
        }catch(\Exception $e) {
          
    }
}
