<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Intervention\Image\Facades\Image;


class ProcessLogo implements ShouldQueue
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
        if($this->type==1){
            $user = User::find($this->id);
            $file  =$user->companyUser->logo;
            $s3 = \Storage::disk('s3_upload');
            $filePath = $this->name;

            $file = \Storage::disk('logos')->get($file);
            $s3->put($filePath, $file, 'public');
        }else{
            $file = $this->name;
            $s3 = \Storage::disk('s3_upload');
            $filePath = $this->name;

            $file = \Storage::disk('logos')->get($file);
            $s3->put($filePath, $file, 'public');
        }
    }
}
