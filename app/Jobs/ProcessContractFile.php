<?php

namespace App\Jobs;

use App\AccountImportationContractFcl as AccountFcl;
use App\AccountImportationContractLcl as AccountLcl;
use App\AccountImportationGlobalcharge as AccountGc;
use App\AccountImportationGlobalChargerLcl as AccountGcLcl;
use App\Carrier;
use App\Jobs\SelectionAutoImportJob;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\NewGlobalchargeRequestFcl;
use App\NewRequestGlobalChargerLcl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProcessContractFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $name;
    protected $type;
    protected $classification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $name, $type, $classification)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->classification = $classification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $classification = $this->classification;

        if (strnatcasecmp($classification, 'request') == 0) {
            if (strnatcasecmp($this->type, 'fcl') == 0) {
                $Ncontracts = NewContractRequest::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('FclRequest')->get($file);
                $file = File::get(storage_path('app/public/Request/Fcl/'.$name));
                $s3->put('Request/FCL/'.$filePath, $file, 'public');
                $extension = pathinfo(storage_path('app/public/Request/Fcl/'.$name), PATHINFO_EXTENSION);
                if (strnatcasecmp($extension, 'xls') == 0 ||
                   strnatcasecmp($extension, 'xlsx') == 0 ||
                   strnatcasecmp($extension, 'csv') == 0) {
                    SelectionAutoImportJob::dispatch($this->id, $this->type);
                }
            } elseif (strnatcasecmp($this->type, 'gcfcl') == 0) {
                $Ncontracts = NewGlobalchargeRequestFcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('GCRequest')->get($file);
                $file = File::get(storage_path('app/public/Request/GC/'.$name));
                $s3->put('Request/Global-charges/FCL/'.$filePath, $file, 'public');
            } elseif (strnatcasecmp($this->type, 'gclcl') == 0) {
                $Ncontracts = NewRequestGlobalChargerLcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('GCRequest')->get($file);
                $file = File::get(storage_path('app/public/Request/GC-LCL/'.$name));
                $s3->put('Request/Global-charges/LCL/'.$filePath, $file, 'public');
            } elseif (strnatcasecmp($this->type, 'lcl') == 0) {
                $Ncontracts = NewContractRequestLcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('LclRequest')->get($file);
                $file = File::get(storage_path('app/public/Request/Lcl/'.$name));
                $s3->put('Request/LCL/'.$filePath, $file, 'public');
                $exists = \Storage::disk('s3_upload')->has('Request/LCL/'.$filePath);
                if(!$exists){
                    \Log::info("El Request numero ".$this->id." No se cargo debidamente" );
                }
            }
        } elseif (strnatcasecmp($classification, 'account') == 0) {
            if (strnatcasecmp($this->type, 'fcl') == 0) {
                $Ncontracts = AccountFcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('FclAccount')->get($file);
                $file = File::get(storage_path('app/public/Account/Fcl/'.$name));
                $s3->put('Account/FCL/'.$filePath, $file, 'public');
            } elseif (strnatcasecmp($this->type, 'gcfcl') == 0) {
                $Ncontracts = AccountGc::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('GCAccount')->get($file);
                $file = File::get(storage_path('app/public/Account/GC/'.$name));
                $s3->put('Account/Global-charges/FCL/'.$filePath, $file, 'public');
            } elseif (strnatcasecmp($this->type, 'gclcl') == 0) {
                $Ncontracts = AccountGcLcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                $file = File::get(storage_path('app/public/Account/GC-LCL/'.$name));
                $s3->put('Account/Global-charges/LCL/'.$filePath, $file, 'public');
            } elseif (strnatcasecmp($this->type, 'lcl') == 0) {
                $Ncontracts = AccountLcl::find($this->id);
                $name = $Ncontracts->namefile;
                $s3 = \Storage::disk('s3_upload');
                $filePath = $this->name;
                //$file       = \Storage::disk('LclAccount')->get($file);
                $file = File::get(storage_path('app/public/Account/Lcl/'.$name));
                $s3->put('Account/LCL/'.$filePath, $file, 'public');
            }
        } elseif (strnatcasecmp($classification, 'carrier') == 0) {
            $carrier = Carrier::find($this->id);
            $nameImg= $carrier->image;
            $file = File::get(public_path('imgcarrier/'.$nameImg));
            Storage::disk('s3_upload')->put('imgcarrier/'.$nameImg, $file, 'public');
        }
    }
}
