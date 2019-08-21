<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Job;

class HelperValidations {

    // VALIDATE IF A JOB IS ASSOCIATED WITH A CONTRACT
    public static function ContractWithJob($contract_id){
        $bool        = false;
        $job_id      = null;
        $jobs = Job::where('payload','like','%contract_id%')->get();
        $jobs = Job::all();
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
        $data = ['bool' => $bool,'job_id' => $job_id];
        //dd($jobs[0]->toArray());

        return $data;
    }
}
