<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\NewContractRequest;

class ProcessContractFile implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $id;
  protected $name;
  /**
     * Create a new job instance.
     *
     * @return void
     */
  public function __construct($id,$name)
  {
    $this->id  = $id;
    $this->name = $name;
  }

  /**
     * Execute the job.
     *
     * @return void
     */
  public function handle()
  {
    $Ncontracts = NewContractRequest::find($this->id);
    $file  =$Ncontracts->namefile;
    $s3 = \Storage::disk('s3_upload');
    $filePath = $this->name;
    $file = \Storage::disk('UpLoadFile')->get($file); 
    $s3->put('contracts/'.$filePath, $file, 'public');
  }
}
