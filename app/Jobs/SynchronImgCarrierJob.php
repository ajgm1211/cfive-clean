<?php

namespace App\Jobs;

use App\Carrier;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SynchronImgCarrierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $carriers = Carrier::all();
        
        // Para Bajar
        foreach($carriers as $carrier){
            try{
                $contents = Storage::disk('s3_upload')->get('imgcarrier/'.$carrier->image);
                $fillbooll  = Storage::disk('carriers')->put($carrier->image,$contents);
            } catch(\Exception $e){
            }
        }

        // Para subir
        foreach($carriers as $carrier){
            try{
                $contents = Storage::disk('carriers')->get($carrier->image);
                $fillbooll = Storage::disk('s3_upload')->put('imgcarrier/'.$carrier->image, $contents, 'public');
            } catch(\Exception $e){
            }
        }
    }
}
