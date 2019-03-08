<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\NewGlobalchargeRequestFcl;

class ProcessContractFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $name;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$name,$type)
    {
        $this->id  = $id;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type == 'fcl'){
            $Ncontracts = NewContractRequest::find($this->id);
            $file  =$Ncontracts->namefile;
            $s3 = \Storage::disk('s3_upload');
            $filePath = $this->name;
            $file = \Storage::disk('UpLoadFile')->get($file); 
            $s3->put('Request/FCL/'.$filePath, $file, 'public');
        }else if($this->type == 'gcfcl'){
            $Ncontracts = NewGlobalchargeRequestFcl::find($this->id);
            $file  =$Ncontracts->namefile;
            $s3 = \Storage::disk('s3_upload');
            $filePath = $this->name;
            $file = \Storage::disk('UpLoadFile')->get($file); 
            $s3->put('Request/Global-charges/FCL/'.$filePath, $file, 'public');
        }else{
            $Ncontracts = NewContractRequestLcl::find($this->id);
            $file  =$Ncontracts->namefile;
            $s3 = \Storage::disk('s3_upload');
            $filePath = $this->name;
            $file = \Storage::disk('UpLoadFile')->get($file); 
            $s3->put('Request/LCL/'.$filePath, $file, 'public');
        }
    }
}
