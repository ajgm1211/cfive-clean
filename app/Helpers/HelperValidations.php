<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Job;
use App\ImportationJob;
use App\NewContractRequest;

class HelperValidations {

    // VALIDATE IF A JOB IS ASSOCIATED WITH A CONTRACT FCL LCL
    public static function ContractWithJob($contract_id){
        $bool        = false;
        $job_id      = null;
        $jobs = Job::where('payload','like','%contract_id%')->get();
        foreach($jobs as $job){
            $poscion    = null;
            $json       = json_decode($job['payload']);
            $poscion    = strripos($json->{'data'}->{'command'},'contract_id');
            $data       = substr($json->{'data'}->{'command'},$poscion,100);
            $data       = explode(";",$data);
            $poscion    = strripos($data[1],':"');
            $data       = substr($data[1],$poscion,100);
            $data       = str_replace([':','"'],'',$data);
            if($data == $contract_id){
                $bool   = true;
                $job_id = $job->id;
            }
        }

        if($bool == false){
            $request_id = null;
            $data       = NewContractRequest::where('contract_id',$contract_id)->first();
            if(!empty($data->id)){
                $request_id = $data->id;
            }
            $jobs       = ImportationJob::where('payload','LIKE','%id%')->get();
            foreach($jobs as $job){
                $poscion    = null;
                $json       = json_decode($job['payload']);
                $poscion    = strripos($json->{'data'}->{'command'},'id');
                $data       = substr($json->{'data'}->{'command'},$poscion,100);
                $data       = explode(";",$data);
                $poscion    = strripos($data[1],':"');
                $data       = substr($data[1],$poscion,100);
                $data       = str_replace([':','"'],'',$data);
                if($data == $request_id){
                    $bool   = true;
                    $job_id = $job->id;
                }
            }
        }

        $data = ['bool' => $bool,'job_id' => $job_id];
        //dd($jobs[0]->toArray());

        return $data;
    }

    // VALIDATE IF A JOB IS ASSOCIATED WITH AN ACOUNT GC
    public static function AcountWithJob($account_id){
        $bool        = false;
        $job_id      = null;
        $jobs = Job::where('payload','like','%account_id%')->get();
        foreach($jobs as $job){
            $poscion    = null;
            $json       = json_decode($job['payload']);
            $poscion    = strripos($json->{'data'}->{'command'},'account_id');
            $data       = substr($json->{'data'}->{'command'},$poscion,100);
            $data       = explode(";",$data);
            $poscion    = strripos($data[1],':"');
            $data       = substr($data[1],$poscion,100);
            $data       = str_replace([':','"'],'',$data);
            if($data == $account_id){
                $bool   = true;
                $job_id = $job->id;
            }
        }
        $data = ['bool' => $bool,'job_id' => $job_id];
        return $data;
    }
}
