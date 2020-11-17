<?php

namespace App\Console\Commands;

use App\LocalCharCarrier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicatesLocalcharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DeleteDuplicatesLocalcharge';

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

            $duplicates = DB::table('localcharcarriers')
                ->select('carrier_id', 'localcharge_id')
                ->groupBy('carrier_id', 'localcharge_id')
                ->havingRaw('COUNT(carrier_id) > ?', [1])
                ->havingRaw('COUNT(localcharge_id) > ?', [1])
                ->get();

            foreach ($duplicates as $item) {

                $localcharcarrier = LocalCharCarrier::where([
                    ['carrier_id', $item->carrier_id],
                    ['localcharge_id', $item->localcharge_id],
                ])->delete();

                $localcharcarrier = new LocalCharCarrier();
                $localcharcarrier->carrier_id = $item->carrier_id;
                $localcharcarrier->localcharge_id = $item->localcharge_id;
                $localcharcarrier->save();
            }
        } catch (\Exception $e) {
            return $this->info($e->getMessage());
        }
        $this->info('Command to delete duplicated localcharcarriers was executed successfully!');
    }
}
