<?php

namespace App\Jobs;

use App\Mail\NewRequestGlobalChargeLclToAdminMail;
use App\Mail\NewRequestGlobalChargeToAdminMail;
use App\Mail\NewRequestLclToAdminMail;
use App\Mail\NewRequestToAdminMail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $type;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (strnatcasecmp($this->type, 'Request-Fcl') == 0) {
            $user_id = $this->data['user'];
            $Ncontract = $this->data['ncontract'];
            $user = User::find($user_id);
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $userNotifique) {
                \Mail::to($userNotifique->email)->send(new NewRequestToAdminMail(
                    $userNotifique->toArray(),
                    $user->toArray(),
                    $Ncontract));
            }
        } elseif (strnatcasecmp($this->type, 'Request-Lcl') == 0) {
            $user_id = $this->data['user'];
            $Ncontract = $this->data['ncontract'];
            $user = User::find($user_id);
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $userNotifique) {
                \Mail::to($userNotifique->email)->send(new NewRequestLclToAdminMail(
                    $userNotifique->toArray(),
                    $user->toArray(),
                    $Ncontract));
            }
        } elseif (strnatcasecmp($this->type, 'Request-Fcl-GC') == 0) {
            $user_id = $this->data['user'];
            $Ncontract = $this->data['ncontract'];
            $user = User::find($user_id);
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $userNotifique) {
                \Mail::to($userNotifique->email)->send(new NewRequestGlobalChargeToAdminMail(
                    $userNotifique->toArray(),
                    $user->toArray(),
                    $Ncontract));
            }
        } elseif (strnatcasecmp($this->type, 'Request-Lcl-GC') == 0) {
            $user_id = $this->data['user'];
            $Ncontract = $this->data['ncontract'];
            $user = User::find($user_id);
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $userNotifique) {
                \Mail::to($userNotifique->email)->send(new NewRequestGlobalChargeLclToAdminMail(
                    $userNotifique->toArray(),
                    $user->toArray(),
                    $Ncontract));
            }
        }
    }
}
