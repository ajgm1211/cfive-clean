<?php

namespace App\Console\Commands;

use App\InlandProvince;
use App\Province;
use Illuminate\Console\Command;

class addedProvincesToNewTable extends Command
{
    //hola
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addedProvincesToNewTable';

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
            $oldProvinces = Province::get()->map(function ($harbor){
                return $harbor->only(['name']);
            });
            foreach($oldProvinces as $provincesName){
                InlandProvince::updateOrCreate(
                    ['name' => $provincesName['name']],
                    [
                        'name' => $provincesName['name'],
                        'country_id' => 66,
                        'region' => ''
                    ]
                );
            }
            \Log::info('done');
        }catch(\Exception $e) {
            \Log::info($e->getMessage());
        }
    }

}
