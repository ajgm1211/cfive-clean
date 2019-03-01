<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\NewContractRequest;
use App\NewContractRequestLcl;

class ProcessContractFile implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $id;
  protected $name;
  protected $tipo;
  /**
     * Create a new job instance.
     *
     * @return void
     */
  public function __construct($id,$name,$tipo)
  {
    $this->id  = $id;
    $this->name = $name;
    $this->tipo = $tipo;
  }

  /**
     * Execute the job.
     *
     * @return void
     */
  public function handle()
  {
    if($this->tipo == 'fcl'){
      $Ncontracts = NewContractRequest::find($this->id);
    }
    if($this->tipo == 'lcl'){
      $Ncontracts = NewContractRequestLcl::find($this->id);
    }

    $file  =$Ncontracts->namefile;
    $s3 = \Storage::disk('s3_upload');
    $filePath = $this->name;
    $file = \Storage::disk('UpLoadFile')->get($file); 
    $s3->put('contracts/'.$filePath, $file, 'public');
  }
}
