<?php

namespace App\Console\Commands;


use App\Inland;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStatusInland extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateStatusInland';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Job that daily checks the expiration dates of the existing Inlands and changes the status for the Inlands that have expired';

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
            $today = Carbon::now();
            $inlands = Inland::where('expire','<=', $today)->where('status', 'publish')->update(array('status'=>'expired'));
            $this->info('The number of Inlands affected is: '.$inlands);
            \Log::info('The number of Inlands affected is: '.$inlands );
        } catch (\Exception $e) {

            $e->getMessage();
        }
    }
}
