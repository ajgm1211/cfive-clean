<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class ProcessLogo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    protected $filepath;
    protected $name;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$filepath,$name,$type)
    {
        $this->id  = $id;
        $this->name = $name;
        $this->filepath = $filepath;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->name;
        $file=File::get(storage_path('app/logos/'.$name));
        $filePath = $this->filepath;
        //$file = Storage::disk('logos')->get($name);
        $s3 = Storage::disk('s3_upload');
        $s3->put($filePath, $file, 'public');
    }
}