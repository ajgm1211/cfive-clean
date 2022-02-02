<?php

namespace App\Console\Commands;

use App\DistanceKmLocation;
use App\InlandDistance;
use App\InlandProvince;
use App\Location;
use Illuminate\Console\Command;

class addedlocationsToNewTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addedlocationsToNewTable';

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
        try{
            $locationOld=InlandDistance::get()->map(function ($locations){
                return $locations->only(['address','province']);
            });

            foreach($locationOld as $location){
                $province_id=InlandProvince::where('name',$location['province']['name'])->first();

                Location::updateOrCreate(
                    ['name'=>$location['address'],'province_id'=>$province_id->id],
                    [ 
                        'name'=>$location['address'],
                        'province_id'=>$province_id->id,
                        'identifier'=>2
                    ]);
            }
            \Log::info('done added all locations');
        }catch(\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
