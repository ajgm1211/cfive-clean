<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ContractLcl;

class RestoreLclFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $from = date('2021-12-16');
        $to = date('2022-02-16');
        $contracts = ContractLcl::select('id')->whereBetween('created_at', [$from, $to])->with('newcontractrequest')->get();
        foreach ($contracts as $contract){
            if ($contract->newcontractrequest){
                $namefile= $contract->newcontractrequest->namefile;

                if ($namefile != null){
                    $s3_file = Storage::disk('s3')->get('Request/LCL/'.$namefile);
                    Storage::disk('LclRequest')->put($namefile, $s3_file);
                    $contract->addMedia(storage_path('app/public/Request/Lcl/' .$namefile))->preservingOriginal()->toMediaCollection('document', 'contracts3');        
                }
                
                break;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
