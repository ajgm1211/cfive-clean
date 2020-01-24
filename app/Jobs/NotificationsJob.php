<?php

namespace App\Jobs;

use App\User;
use App\Mail\NewRequestToAdminMail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotificationsJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $type, $data;
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
		if(strnatcasecmp($this->type,'Request-Fcl') == 0){
			$user_id 	= $this->data['user'];
			$Ncontract	= $this->data['ncontract'];
			$user 		= User::find($user_id);
			$admins 	= User::where('type','admin')->get();
			foreach($admins as $userNotifique){
				\Mail::to($userNotifique->email)->send(new NewRequestToAdminMail(
					$userNotifique->toArray(),
					$user->toArray(),
					$Ncontract));
			}
		} elseif(strnatcasecmp($this->type,'Request-Lcl') == 0){

		} elseif(strnatcasecmp($this->type,'Request-Fcl-GC') == 0){

		} elseif(strnatcasecmp($this->type,'Request-Lcl-GC') == 0){

		}
	}
}
