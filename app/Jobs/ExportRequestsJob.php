<?php

namespace App\Jobs;

use Excel;
use PrvRequest;
use Illuminate\Bus\Queueable;
use App\Mail\ExportRequestsAll;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $dateStart,$dateEnd,$auth,$selector;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dateStart,$dateEnd,$auth,$selector)
    {
        $this->dateStart    = $dateStart;
        $this->dateEnd      = $dateEnd;
        $this->auth         = $auth;
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
        $auth       = $this->auth;
        $selector   = $this->selector;
        $now        = new \DateTime();
        $now        = $now->format('dmY_His');
        if(strnatcasecmp($selector,'fcl') == 0){
            $nameFile   = 'Request_Fcl_'.$now;
            $data       = PrvRequest::RequestFclBetween($dateStart,$dateEnd);
            $myFile = Excel::create($nameFile, function($excel) use($data) {

                $excel->sheet('REQUEST_FCL', function($sheet) use($data) {
                    $sheet->cells('A1:M1', function($cells) {
                        $cells->setBackground('#2525ba');
                        $cells->setFontColor('#ffffff');
                        //$cells->setValignment('center');
                    });

                    $sheet->setWidth(array(
                        'A'     =>  10,
                        'B'     =>  30,
                        'C'     =>  25,
                        'D'     =>  10,
                        'E'     =>  20,
                        'F'     =>  25,
                        'G'     =>  15,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  25,
                        'K'     =>  25,
                        'L'     =>  15,
                        'M'     =>  15
                    ));

                    $sheet->row(1, array(
                        "Id",
                        "Company",
                        "Reference",
                        "Direction",
                        "Carrier",
                        "Validation",
                        "Date",
                        "User",
                        "Username load",
                        "Time Start",
                        "Time End",
                        "Time Elapsed",
                        "Status"
                    ));
                    $i= 2;

                    $data   = $data->chunk(500);
                    $data   = $data->toArray();;
                    foreach($data as $nrequests){
                        foreach($nrequests as $nrequest){                   
                            $sheet->row($i, array(
                                "Id"                => $nrequest['id'],
                                "Company"           => $nrequest['company'],
                                "Reference"         => $nrequest['reference'],
                                "Direction"         => $nrequest['direction'],
                                "Carrier"           => $nrequest['carrier'],
                                "Validation"        => $nrequest['validation'],
                                "Date"              => $nrequest['date'],
                                "User"              => $nrequest['user'],
                                "Username load"     => $nrequest['username_load'],
                                "Time Start"        => $nrequest['time_start'],
                                "Time End"          => $nrequest['time_end'],
                                "Time Elapsed"      => $nrequest['time_elapsed'],
                                "Status"            => $nrequest['status']
                            ));
                            $sheet->setBorder('A1:M'.$i, 'thin');

                            $sheet->cells('F'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });
                            
                            $sheet->cells('K'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });
                            
                            $sheet->cells('J'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });
                            
                            $i++;
                        }
                    }
                });

            })->store('xlsx',storage_path('app/exports'));
            \Mail::to($auth['email'])->send(new ExportRequestsAll($nameFile.'.xlsx','FCL'));
            Storage::disk('RequestFiles')->delete($nameFile.'.xlsx');
        } else if(strnatcasecmp($selector,'lcl') == 0){
            $nameFile   = 'Request_Lcl_'.$now;
            $data       = PrvRequest::RequestLclBetween($dateStart,$dateEnd);
            $myFile = Excel::create($nameFile, function($excel) use($data) {

                $excel->sheet('REQUEST_LCL', function($sheet) use($data) {
                    $sheet->cells('A1:M1', function($cells) {
                        $cells->setBackground('#2525ba');
                        $cells->setFontColor('#ffffff');
                        //$cells->setValignment('center');
                    });

                    $sheet->setWidth(array(
                        'A'     =>  10,
                        'B'     =>  30,
                        'C'     =>  25,
                        'D'     =>  10,
                        'E'     =>  20,
                        'F'     =>  25,
                        'G'     =>  25,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  25,
                        'K'     =>  25,
                        'L'     =>  15,
                        'M'     =>  15
                    ));

                    $sheet->row(1, array(
                        "Id",
                        "Company",
                        "Reference",
                        "Direction",
                        "Carrier",
                        "Validation",
                        "Date",
                        "User",
                        "Username load",
                        "Time Start",
                        "Time End",
                        "Time Elapsed",
                        "Status"
                    ));
                    $i= 2;

                    $data   = $data->chunk(500);
                    $data   = $data->toArray();;
                    foreach($data as $nrequests){
                        foreach($nrequests as $nrequest){                   
                            $sheet->row($i, array(
                                "Id"                => $nrequest['id'],
                                "Company"           => $nrequest['company'],
                                "Reference"         => $nrequest['reference'],
                                "Direction"         => $nrequest['direction'],
                                "Carrier"           => $nrequest['carrier'],
                                "Validation"        => $nrequest['validation'],
                                "Date"              => $nrequest['date'],
                                "User"              => $nrequest['user'],
                                "Username load"     => $nrequest['username_load'],
                                "Time Start"        => $nrequest['time_start'],
                                "Time End"          => $nrequest['time_end'],
                                "Time Elapsed"      => $nrequest['time_elapsed'],
                                "Status"            => $nrequest['status']
                            ));
                            $sheet->setBorder('A1:M'.$i, 'thin');

                            $sheet->cells('F'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });
                            
                            $sheet->cells('G'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });

                            $sheet->cells('K'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });

                            $sheet->cells('J'.$i, function($cells) {
                                $cells->setAlignment('center');
                            });

                            $i++;
                        }
                    }
                });

            })->store('xlsx',storage_path('app/exports'));
            \Mail::to($auth['email'])->send(new ExportRequestsAll($nameFile.'.xlsx','LCL'));
            Storage::disk('RequestFiles')->delete($nameFile.'.xlsx');
        }
    }
}
