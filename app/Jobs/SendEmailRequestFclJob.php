<?php

namespace App\Jobs;

use App\Mail\NewRequestToAdminMail;
use App\Mail\RequestToUserMail;
use App\NewContractRequest;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PrvUserConfigurations;

class SendEmailRequestFclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $usercreador;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($usercreador, $id)
    {
        $this->usercreador = $usercreador;
        $this->id = $id;
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
        $Ncontract = NewContractRequest::find($id);
        if ($Ncontract->sentemail == false) {
            $usersCompa = User::all()->where('type', '=', 'company')->where('company_user_id', '=', $Ncontract->company_user_id);
            foreach ($usersCompa as $userCmp) {
                if ($userCmp->id != $Ncontract->user_id) {
                    $json = PrvUserConfigurations::allData($userCmp->id);
                    if ($json['notifications']['request-importation-fcl']) {
                        \Mail::to($userCmp->email)->send(new RequestToUserMail($userCmp->toArray(),
                                                                               $Ncontract->toArray()));
                    }
                }
            }
            $json = PrvUserConfigurations::allData($usercreador['id']);
            if ($json['notifications']['request-importation-fcl']) {
                \Mail::to($usercreador['email'])->send(new RequestToUserMail($usercreador,
                                                                             $Ncontract->toArray()));
            }
            $Ncontract->sentemail = true;
        }
        $Ncontract->save();
    }
}
