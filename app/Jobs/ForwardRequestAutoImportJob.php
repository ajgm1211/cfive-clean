<?php

namespace App\Jobs;

use App\NewContractRequest;
use App\Jobs\SelectionAutoImportJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForwardRequestAutoImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $dateStart,$dateEnd,$selector;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dateStart,$dateEnd,$selector)
    {
        $this->dateStart    = $dateStart;
        $this->dateEnd      = $dateEnd;
        $this->selector     = $selector;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dateStart  = $this->dateStart;
        $dateEnd    = $this->dateEnd;
        $selector   = $this->selector;

        if(strnatcasecmp($selector,'fcl') == 0){
            $status     = 'Pending';
            $requets    = NewContractRequest::whereBetween('created',[$dateStart,$dateEnd])->get();
            foreach($requets as $requet){
                $existsS3 = Storage::disk('s3_upload')->exists('Request/FCL/'.$requet->namefile);
                if($existsS3 == true && $requet->status == $status){
                    SelectionAutoImportJob::dispatch($requet->id,'fcl');
                } else{
                    $existsLocal = Storage::disk('FclRequest')->exists($requet->namefile);
                    if($existsLocal){
                        $name   = $requet->namefile;
                        $s3     = Storage::disk('s3_upload');
                        $file   = File::get(storage_path('app/public/Request/Fcl/'.$name));
                        $s3     = $s3->put('Request/FCL/'.$name, $file, 'public');
                        if($s3 == true && $requet->status == $status){
                            SelectionAutoImportJob::dispatch($requet->id,'fcl');
                        }
                    }
                }
            }
        }
    }
}
