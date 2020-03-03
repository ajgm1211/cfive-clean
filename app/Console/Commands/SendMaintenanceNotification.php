<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\SendMaintenanceNotificationEmail;

class SendMaintenanceNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenanceNotification:send {day} {month} {date} {hour} {duration}';

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
        $day = $this->argument('day');
        $month = $this->argument('month');
        $date = $this->argument('date');
        $hour = $this->argument('hour');
        $duration = $this->argument('duration');
        try{
            $users=User::where('state',1)->get();
            foreach ($users as $item) {
                Mail::to($item->email)->send(new SendMaintenanceNotificationEmail($day, $month, $date, $hour, $duration));
            }
        } catch(\Exception $e){
            return $this->info($e->getMessage());
        }
        return $this->info('Command Send Maintenance Notification executed successfully!');
    }
}
