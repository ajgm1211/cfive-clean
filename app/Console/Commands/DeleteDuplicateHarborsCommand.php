<?php

namespace App\Console\Commands;

use App\GlobalCharCountryPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharPortCountry;
use App\GlobalCharPortLcl;
use App\Harbor;
use App\LocalCharPort;
use App\Rate;
use App\RateLcl;
use App\TransitTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateHarborsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteDuplicateHarbors {duplicate} {original}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to delete duplicate harbors from database, the first parameter is the duplicate ID and the second is the original ID';

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

            $duplicate = $this->argument('duplicate');
            $original = $this->argument('original');

            LocalCharPort::where('port_orig', $duplicate)->update(['port_orig' => $original]);
            LocalCharPort::where('port_dest', $duplicate)->update(['port_dest' => $original]);
            GlobalCharPort::where('port_orig', $duplicate)->update(['port_orig' => $original]);
            GlobalCharPort::where('port_dest', $duplicate)->update(['port_dest' => $original]);
            GlobalCharPortLcl::where('port_orig', $duplicate)->update(['port_orig' => $original]);
            GlobalCharPortLcl::where('port_dest', $duplicate)->update(['port_dest' => $original]);
            GlobalCharPortCountry::where('port_orig', $duplicate)->update(['port_orig' => $original]);
            GlobalCharCountryPort::where('port_dest', $duplicate)->update(['port_dest' => $original]);
            Rate::where('origin_port', $duplicate)->update(['origin_port' => $original]);
            Rate::where('destiny_port', $duplicate)->update(['destiny_port' => $original]);
            RateLcl::where('origin_port', $duplicate)->update(['origin_port' => $original]);
            RateLcl::where('destiny_port', $duplicate)->update(['destiny_port' => $original]);
            TransitTime::where('origin_id', $duplicate)->update(['origin_id' => $original]);
            TransitTime::where('destination_id', $duplicate)->update(['destination_id' => $original]);

            Harbor::where('id', $duplicate)->delete();

        } catch (\Exception $e) {
            return $this->info($e->getMessage());
        }
        $this->info('Command to delete duplicated harbors was executed successfully!');
    }
}
