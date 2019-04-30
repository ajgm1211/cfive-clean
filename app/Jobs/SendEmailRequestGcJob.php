<?php

namespace App\Jobs;

use App\User;
use App\NewGlobalchargeRequestFcl;
use App\Mail\NewRequestGlobalChargeToUserMail;
use App\Mail\NewRequestGlobalChargeToAdminMail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailRequestGcJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $usercreador,$id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($usercreador,$id)
    {
        $this->usercreador  = $usercreador;
        $this->id           = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $usercreador    = $this->usercreador;
        $id             = $this->id;
        $Ncontract = NewGlobalchargeRequestFcl::find($id);
        if($Ncontract->sentemail == false){
            $usersCompa = User::all()->where('type','=','company')->where('company_user_id','=',$Ncontract->company_user_id);
            foreach ($usersCompa as $userCmp) {
                if($userCmp->id != $Ncontract->user_id){
                    \Mail::to($userCmp->email)->send(new NewRequestGlobalChargeToAdminMail($userCmp->toArray(),$Ncontract->toArray()));
                }
            }
            
            \Mail::to($usercreador['email'])->send(new NewRequestGlobalChargeToUserMail($usercreador,
                                                                                      $Ncontract->toArray()));
            $Ncontract->sentemail = true;
        }
        $Ncontract->save();
    }
}
