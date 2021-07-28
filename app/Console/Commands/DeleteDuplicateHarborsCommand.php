<?php

namespace App\Console\Commands;

use App\Duplicados;
use App\GlobalCharCountryPort;
use App\GlobalCharPort;
use App\GlobalCharPortCountry;
use App\GlobalCharPortLcl;
use App\LocalCharPort;
use App\Rate;
use App\Harbor;
use App\RateLcl;
use App\InlandPort;
use App\RemarkHarbor;
use App\AutomaticRateTotal;
use App\AutomaticInland;
use App\AutomaticRate;
use App\AutomaticInlandTotal;
use App\AutomaticInlandLclAir;







use App\TransitTime;
use Illuminate\Console\Command;

class DeleteDuplicateHarborsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteDuplicateHarbors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to delete duplicate harbors from database, the first parameter is the original ID and the second one is the duplicate ID';

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

            $objDuplicados = Duplicados::get();

            foreach ($objDuplicados as $registrosDuplicados) {

                $harborsDuplicados = json_decode($registrosDuplicados->duplicados);

                foreach ($harborsDuplicados as $idDuplicados) {

                    $original = $registrosDuplicados->id_original;
                    $duplicate = (int) $idDuplicados;

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
                    InlandPort::where('port', $duplicate)->update(['port' => $original]);
                    RemarkHarbor::where('port_id', $duplicate)->update(['port_id' => $original]);
                    AutomaticRateTotal::where('destination_port_id', $duplicate)->update(['destination_port_id' => $original]);
                    AutomaticRateTotal::where('origin_port_id', $duplicate)->update(['origin_port_id' => $original]);
                    AutomaticRate::where('destination_port_id', $duplicate)->update(['destination_port_id' => $original]);
                    AutomaticRate::where('origin_port_id', $duplicate)->update(['origin_port_id' => $original]);
                    AutomaticInland::where('port_id', $duplicate)->update(['port_id' => $original]);
                    AutomaticInlandTotal::where('port_id', $duplicate)->update(['port_id' => $original]);
                    AutomaticInlandLclAir::where('port_id', $duplicate)->update(['port_id' => $original]);

                    Harbor::where('id', $duplicate)->delete();

                }

                Harbor::where('id', $original)->update(['varation' => $registrosDuplicados->varation]);

            }

        } catch (\Exception $e) {
            return $this->info($e->getMessage());
        }
        $this->info('Command to delete duplicated harbors was executed successfully!');
    }
}
