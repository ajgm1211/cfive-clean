<?php

namespace App\Console\Commands;

use App\DistanceKmLocation;
use App\HarborsLocationSearch;
use Illuminate\Console\Command;

class addDataToHarborLocationSearchTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addDataToHarborLocationSearchTable';

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
            $LocationHarbors=DistanceKmLocation::get()->map(function ($locations){
                return $locations->only(['location_id','harbors_id']);
            });

            foreach($LocationHarbors as $data){
                HarborsLocationSearch::updateOrCreate(
                    ['location_id'=>$data['location_id'],'harbors_id'=>$data['harbors_id']],
                    [
                        'harbors_id'=>$data['harbors_id'],
                        'location_id'=>$data['location_id']
                    ]);
            }
            \Log::info('done harbor location search');
        }catch(\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
