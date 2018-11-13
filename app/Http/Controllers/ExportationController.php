<?php

namespace App\Http\Controllers;

use Excel;
use PrvRates;
use DataTime;
use App\Contract;
use PrvSurchargers;
use Illuminate\Http\Request;

class ExportationController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        /*$now = new \DataTime();
       $now = $now->format('dmY_His');*/
        $contract = Contract::find($id);
        Excel::create($contract['name'], function($excel) use($id,$contract) {
            $excel->sheet('Contract', function($sheet) use($contract) {
                //dd($contract);
                $sheet->cells('A1:D1', function($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    $cells->setValignment('center');
                });
                $sheet->setBorder('A1:D2', 'thin');

                $sheet->row(1, array(
                    "Name",
                    "Number",
                    "Validity",
                    "Expire"
                ));
                $sheet->row(2, array(
                    "Origin"    => $contract['name'],
                    "Destiny"   => $contract['number'],
                    "Carrier"   => $contract['validity'],
                    "20'"       => $contract['expire'],
                ));
            });

            $excel->sheet('Rates', function($sheet) use($id) {
                $sheet->cells('A1:I1', function($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('C1:I1', function($cells) {
                        $cells->setAlignment('center');
                    });

                $sheet->setWidth(array(
                    'A'     =>  25,
                    'B'     =>  25,
                    'C'     =>  11,
                    'D'     =>  10,
                    'E'     =>  10,
                    'F'     =>  10,
                    'G'     =>  10,
                    'H'     =>  10,
                    'I'     =>  11
                ));
                #

                $rates = PrvRates::get_rates($id);
                $sheet->row(1, array(
                    "Origin",
                    "Destiny",
                    "Carrier",
                    "20'",
                    "40'",
                    "40'HC",
                    "40'NOR",
                    "45'",
                    "Currency"
                ));
                $i= 2;
                foreach($rates as $rate){
                    $sheet->row($i, array(
                        "Origin"    => $rate['origin_portLb'],
                        "Destiny"   => $rate['destiny_portLb'],
                        "Carrier"   => $rate['carrierLb'],
                        "20'"       => $rate['twuenty'],
                        "40'"       => $rate['forty'],
                        "40'HC"     => $rate['fortyhc'],
                        "40'NOR"    => $rate['fortynor'],
                        "45'"       => $rate['fortyfive'],
                        "Currency"  => $rate['currency_id']
                    ));
                    $sheet->setBorder('A1:I'.$i, 'thin');
                    
                    $sheet->cells('C'.$i, function($cells) {
                        $cells->setAlignment('center');
                    });
                    
                    $sheet->cells('I'.$i, function($cells) {
                        $cells->setAlignment('center');
                    });
                    
                    $i++;

                }
            });

            $excel->sheet('Surchargers', function($sheet) use($id) {
                $sheet->cells('A1:H1', function($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    //$cells->setValignment('center');
                });

                $sheet->setWidth(array(
                    'A'     =>  11,
                    'B'     =>  25,
                    'C'     =>  25,
                    'D'     =>  20,
                    'E'     =>  15,
                    'F'     =>  10,
                    'G'     =>  20,
                    'H'     =>  10
                ));

                $surchargers = PrvSurchargers::get_surchargers($id);
                $sheet->row(1, array(
                    "Surcharge",
                    "Origin",
                    "Destiny",
                    "Carrier",
                    "Type Destiny",
                    "Ammount",
                    "Calculation Type",
                    "Currency"
                ));
                $i= 2;
                foreach($surchargers as $surcharger){
                    // dd($surcharger);
                    $sheet->row($i, array(
                        "surcharge"       => $surcharger['surchargelb'],
                        "Origin"          => $surcharger['origin_portLb'],
                        "Destiny"         => $surcharger['destiny_portLb'],
                        "Carrier"         => $surcharger['carrierlb'],
                        "typedestiny"     => $surcharger['typedestinylb'],
                        "Ammount"         => $surcharger['ammount'],
                        "calculationtype" => $surcharger['calculationtypelb'],
                        "Currency"        => $surcharger['currencylb']
                    ));
                    $sheet->setBorder('A1:H'.$i, 'thin');
                    
                    $sheet->cells('E'.$i, function($cells) {
                        $cells->setAlignment('center');
                    });

                    $sheet->cells('H'.$i, function($cells) {
                        $cells->setAlignment('center');
                    });
                    $i++;
                }
            });

        })->download('xlsx');
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
