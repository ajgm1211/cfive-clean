<?php

namespace App\Jobs;

use App\Mail\NewRequestGlobalChargeLclToAdminMail;
use App\Mail\NewRequestGlobalChargeLclToUsernMail;
use App\Mail\NewRequestGlobalChargeToAdminMail;
use App\Mail\NewRequestGlobalChargeToUserMail;
use App\NewGlobalchargeRequestFcl;
use App\NewRequestGlobalChargerLcl;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PrvUserConfigurations;

class SendEmailRequestGcJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $usercreador;
    protected $id;
    protected $selector;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($usercreador, $id, $selector)
    {
        $this->usercreador = $usercreador;
        $this->id = $id;
        $this->selector = $selector;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $usercreador = $this->usercreador;
        $id = $this->id;
        $selector = $this->selector;
        if ($selector == 'fcl') {
            $Ncontract = NewGlobalchargeRequestFcl::find($id);
            if ($Ncontract->sentemail == false) {
                $usersCompa = User::all()->where('type', '=', 'company')->where('company_user_id', '=', $Ncontract->company_user_id);
                foreach ($usersCompa as $userCmp) {
                    if ($userCmp->id != $Ncontract->user_id) {
                        $json = PrvUserConfigurations::allData($userCmp->id);
                        if ($json['notifications']['request-importation-gcfcl']) {
                            \Mail::to($userCmp->email)->send(new NewRequestGlobalChargeToAdminMail($userCmp->toArray(), $Ncontract->toArray()));
                        }
                    }
                }

                $json = PrvUserConfigurations::allData($usercreador['id']);
                if ($json['notifications']['request-importation-gcfcl']) {
                    \Mail::to($usercreador['email'])->send(new NewRequestGlobalChargeToUserMail($usercreador,
                                                                                                $Ncontract->toArray()));
                }
                $Ncontract->sentemail = true;
            }
            $Ncontract->save();
        } elseif (strnatcasecmp($selector, 'lcl') == 0) {
            $Ncontract = NewRequestGlobalChargerLcl::find($id);
            if ($Ncontract->sentemail == false) {
                $usersCompa = User::all()->where('type', '=', 'company')->where('company_user_id', '=', $Ncontract->company_user_id);
                foreach ($usersCompa as $userCmp) {
                    if ($userCmp->id != $Ncontract->user_id) {
                        $json = PrvUserConfigurations::allData($userCmp->id);
                        if ($json['notifications']['request-importation-gclcl']) {
                            \Mail::to($userCmp->email)->send(new NewRequestGlobalChargeLclToAdminMail($userCmp->toArray(), $Ncontract->toArray()));
                        }
                    }
                }

                $json = PrvUserConfigurations::allData($usercreador['id']);
                if ($json['notifications']['request-importation-gclcl']) {
                    \Mail::to($usercreador['email'])->send(new NewRequestGlobalChargeLclToUsernMail($usercreador,
                                                                                                $Ncontract->toArray()));
                }
                $Ncontract->sentemail = true;
            }
            $Ncontract->save();
        }
    }
}
