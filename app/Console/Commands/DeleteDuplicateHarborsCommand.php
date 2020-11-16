<?php

namespace App\Console\Commands;

use App\GlobalCharge;
use App\GlobalCharPort;
use App\Harbor;
use App\LocalCharPort;
use App\Rate;
use App\RateLcl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateHarborsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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

            $array = [2912, 1552, 1954, 805];
            $duplicate = [2913, 2298, 2566, 2757];

            foreach ($array as $item) {
                foreach ($duplicate as $value) {
                    LocalCharPort::where('port_orig',$value)->update(['port_orig'=>$item]);
                    LocalCharPort::where('port_dest',$value)->update(['port_dest'=>$item]);
                    GlobalCharge::where('port_orig',$value)->update(['port_orig'=>$item]);
                    GlobalCharge::where('port_dest',$value)->update(['port_dest'=>$item]);
                    GlobalCharPort::where('port_orig', $value)->update(['port_orig'=>$item]);
                    Rate::where('origin_port', $value)->update(['origin_port'=>$item]);
                    Rate::where('destiny_port', $value)->update(['destiny_port'=>$item]);
                    RateLcl::where('origin_port', $value)->update(['origin_port'=>$item]);
                    RateLcl::where('destiny_port', $value)->update(['destiny_port'=>$item]);

                    Harbor::where('id',$value)->delete();
                }
            }

            /*UPDATE global_char_port_countries set port_orig = 1721 where port_orig=2418;
            UPDATE ebdb.localcharports set port_orig = 747 where port_orig = 2418;
            UPDATE ebdb.localcharports set port_dest = 1721 where port_dest = 2418;
            UPDATE ebdb.globalcharport set port_orig = 747 where port_orig = 2418;
            UPDATE ebdb.globalcharport set port_dest = 1721 where port_dest = 2418;
            UPDATE rates set origin_port = 1721 where origin_port=2418;
            UPDATE rates set destiny_port = 1721 where destiny_port=2418;
            UPDATE rates_lcl set origin_port = 1721 where origin_port=2418;
            UPDATE rates_lcl set destiny_port = 1721 where destiny_port=2418;*/

        } catch (\Exception $e) {
            return $this->info($e->getMessage());
        }
        $this->info('Command to delete duplicated harbors was executed successfully!');
    }
}
