<?php

namespace App\Jobs;

use App\CalculationType;
use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Currency;
use App\Mail\EmailForExcelFile;
use App\Rate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class SendExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subject;
    protected $body;
    protected $to;
    protected $quote;
    protected $email;
    protected $data;
    protected $company_user;
    protected $id_user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $to, $email, $data, $company_user, $id_user)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->email = $email;
        $this->data = $data;
        $this->company_user = $company_user;
        $this->id_user = $id_user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $origin_port = $this->data['data']['origin_port'];
        $destiny_port = $this->data['data']['destination_port'];
        $direction = $this->data['data']['direction']; //'2020/10/01';
        $code = $this->data['data']['gp_container']; //'2020/10/01';
        $containers = Container::where('gp_container_id', $code)->get();
        $dateSince = $this->data['data']['validity']; //'2020/10/01';
        $dateUntil = $this->data['data']['expire']; //'2020/10/30';
        $company_id = '';
        $company_user_id = $this->company_user;
        $user_id = $this->id_user;
        $company_setting = CompanyUser::where('id', $company_user_id)->first();
        $container_calculation = ContainerCalculation::get();
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
        );
        $styleArrayALL = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'CD0000'),
            ),
        );

        if ($direction == 3) {
            $direction = array(1, 2, 3);
        } else {
            $direction = array($direction);
        }

        // Construcion Del header Inicial
        $arrayComplete = $this->get_header_inicial($containers);

        $arreglo = Rate::whereIn('origin_port', $origin_port)->whereIn('destiny_port', $destiny_port)->with('port_origin', 'port_destiny', 'contract', 'carrier')->whereHas('contract', function ($q) use ($dateSince, $dateUntil, $company_user_id, $company_setting, $direction, $code) {
            if ($company_setting->future_dates == 1) {
                $q->where(function ($query) use ($dateSince) {
                    $query->where('validity', '>=', $dateSince)->orwhere('expire', '>=', $dateSince);
                })->where('company_user_id', '=', $company_user_id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code);
            } else {
                $q->where(function ($query) use ($dateSince, $dateUntil) {
                    $query->where('validity', '<=', $dateSince)->where('expire', '>=', $dateUntil);
                })->where('company_user_id', '=', $company_user_id)->whereIn('direction_id', $direction)->where('status', '!=', 'incomplete')->where('gp_container_id', $code);
            }
        })->orderBy('contract_id')->get();

        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nameFile = str_replace([' '], '_', $now . '_rates');
        $file = Excel::create($nameFile, function ($excel) use ($nameFile, $arreglo, $arrayComplete, $containers, $container_calculation, $styleArray,$styleArrayALL) {
            $excel->sheet('Rates', function ($sheet) use ($arreglo, $arrayComplete, $containers, $container_calculation, $styleArray,$styleArrayALL) {

                $sheet->cells('A1:AG1', function ($cells) {
                    $cells->setBackground('#2525ba');
                    $cells->setFontColor('#ffffff');
                    $cells->setValignment('center');
                });

                $sheet->row(1, $arrayComplete);
                $a = 2;
                $contractId = -1;
                foreach ($arreglo as $data) {

                    $montos = array();
                    $montos2 = array();
                    $montosAllIn = array();
                    $montosAllInTot = array();
                    foreach ($containers as $cont) {
                        $name_rate = 'rate' . $cont->code;

                        $var = 'array' . $cont->code;
                        $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();

                        $options = json_decode($cont->options);
                        //dd($options);
                        if (@$options->field_rate == 'containers') {
                            $test = json_encode($data->{$options->field_rate});
                            $jsonContainer = json_decode($data->{$options->field_rate});
                            if (isset($jsonContainer->{'C' . $cont->code})) {
                                $rateMount = $jsonContainer->{'C' . $cont->code};
                                $$name_rate = $rateMount;
                                $montosAllIn = array($cont->code => $$name_rate);
                            } else {
                                $rateMount = 0;
                                $$name_rate = $rateMount;
                                $montosAllIn = array($cont->code => $$name_rate);
                            }
                        } else {
                            $rateMount = $data->{$options->field_rate};
                            $$name_rate = $rateMount;
                            $montosAllIn = array($cont->code => $$name_rate);
                        }

                        $montos2 = array($cont->code => $rateMount);
                        $montos = array_merge($montos, $montos2);
                        $montosAllInTot = array_merge($montosAllInTot, $montosAllIn);

                    }
                    //
                    $sheet->getStyle('A'.$a.':O'.$a.'')->applyFromArray($styleArray);

                    $arrayCompleteAmount = $this->get_header_rate($data, $montos);

                    $sheet->row($a, $arrayCompleteAmount);
                    $a++;
                    // Local charges

                    $localCharge = \DB::select(\DB::raw('call proc_getLocalChargeExcel(' . $data->contract->id . ',' . $data->port_origin->id . ',' . $data->port_destiny->id . ')'));
                    if ($localCharge != null) {

                        for ($i = 0; $i < count($localCharge); $i++) {
                            $montosLocal = array();
                            $montosLocal2 = array();

                            foreach ($containers as $cont) {
                                $name_arreglo = 'array' . $cont->code;
                                $name_rate = 'rate' . $cont->code;
                                if (in_array($localCharge[$i]->calculation_type_id, $$name_arreglo)) {
                                    $monto = $this->perTeu($localCharge[$i]->ammount, $localCharge[$i]->calculation_type_id, $cont->code);
                                    $currency_rate = $this->ratesCurrency($localCharge[$i]->currency_id, $data->currency->alphacode);
                                    $$name_rate = number_format($$name_rate + ($monto / $currency_rate), 2, '.', '');
                                    $montosAllInTot[$cont->code] = $$name_rate;
                                    $montosLocal2 = array($cont->code => $monto);
                                    $montosLocal = array_merge($montosLocal, $montosLocal2);
                                } else {
                                    $montosLocal2 = array($cont->code => '0');

                                    $montosLocal = array_merge($montosLocal, $montosLocal2);
                                }
                            }

                            $arrayCompleteLocal = $this->get_header_localcharge($localCharge[$i], $data, $montosLocal);

                            $sheet->row($a, $arrayCompleteLocal);
                            $a++;
                        }

                    }

                    //Montos All IN
                    $sheet->getStyle('G' . $a)->applyFromArray($styleArrayALL);
                    $arrayCompleteAmountAllIn = $this->get_amount_allIn($data,$montosAllInTot);
                    $sheet->row($a, $arrayCompleteAmountAllIn);
                    $a++;
                    //Fin montos all in 

                    $i = 1;
                    $sheet->setBorder('A1:I' . $i, 'thin');
                    $sheet->cells('C' . $a, function ($cells) {
                        $cells->setAlignment('center');
                    });
                    $sheet->cells('I' . $a, function ($cells) {
                        $cells->setAlignment('center');
                    });
                }
            });
        })->store('xls', storage_path('excel/exports'));

        //$path = storage_path('excel/exports') . '/' . $nameFile . '.xls'; // or storage_path() if needed
        $nameFile = $nameFile . '.xls';

        $fileExcel = \File::get(storage_path('excel/exports') . '/' . $nameFile);
        // \Log::info('Store file excel');
        \Storage::disk('s3_upload')->put('contract_excel/' . $nameFile, $fileExcel, 'public');
        //\Log::info('Push in s3');
        $descarga = \Storage::disk('s3_upload')->url('contract_excel/' . $nameFile, $nameFile);
        //\Log::info('Link of download');

        try {
            if ($this->to != '') {

                \Mail::to($this->email)->send(new EmailForExcelFile($this->subject, $descarga, $nameFile));

            } else {
                \Mail::to($this->email)->send(new EmailForExcelFile($this->subject, $descarga, $nameFile));
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }

    }

    public function get_header_inicial($containers)
    {

        $contHeaderArray = $containers->pluck('code')->toArray();
        $arrayHeaderFirstPart = array(
            'Contract',
            'Reference',
            'Carrier',
            'Direction',
            'Origin',
            'Destination',
            'Charge',

        );
        $arrayHeaderFirstPart = array_merge($arrayHeaderFirstPart, $contHeaderArray);
        $arrayHeaderSecondPart = array(
            'Currency',
            'From',
            'Until',
        );
        $arrayComplete = array_merge($arrayHeaderFirstPart, $arrayHeaderSecondPart);
        return $arrayComplete;
    }

    public function get_header_rate($data, $montos)
    {

        $arrayFirstPartAmount = array(
            'Contract' => $data->contract->name,
            'Reference' => $data->contract->id,
            'Carrier' => $data->carrier->name,
            'Direction' => $data->contract->direction->name,
            'Origin' => ucwords(strtolower($data->port_origin->name)),
            'Destination' => ucwords(strtolower($data->port_destiny->name)),
            'Charge' => htmlentities('Freight'),
        );
        $arrayFirstPartAmount = array_merge($arrayFirstPartAmount, $montos);
        $arraySecondPartAmount = array(
            'Currency' => $data->currency->alphacode,
            'From' => $data->contract->validity,
            'Until' => $data->contract->expire,

        );
        $arrayCompleteAmount = array_merge($arrayFirstPartAmount, $arraySecondPartAmount);

        return $arrayCompleteAmount;

    }
    public function get_header_localcharge($localCharge, $data, $montosLocal)
    {

        $arrayFirstPartLocal = array(
            'Contract' => $data->contract->name,
            'Reference' => $data->contract->id,
            'Carrier' => $localCharge->carrier,
            'Direction' => $data->contract->direction->name,
            'Origin' => $localCharge->port_orig,
            'Destination' => $localCharge->port_dest,
            'Charge' => $localCharge->surcharge,

        );

        $arrayFirstPartLocal = array_merge($arrayFirstPartLocal, $montosLocal);

        $arraySecondPartLocal = array(
            'Currency' => $localCharge->currency,
            'From' => $data->contract->validity,
            'Until' => $data->contract->expire,
        );
        $arrayCompleteLocal = array_merge($arrayFirstPartLocal, $arraySecondPartLocal);

        return $arrayCompleteLocal;

    }

    public function get_amount_allIn($data,$montosAllInTot )
    {

        $arrayFirstPartAmountAllIn = array(
            'Contract' => $data->contract->name,
            'Reference' => $data->contract->id,
            'Carrier' => $data->carrier->name,
            'Direction' => $data->contract->direction->name,
            'Origin' => ucwords(strtolower($data->port_origin->name)),
            'Destination' => ucwords(strtolower($data->port_destiny->name)),
            'Charge' => 'Freight - ALL IN',
        );
        $arrayFirstPartAmountAllIn = array_merge($arrayFirstPartAmountAllIn, $montosAllInTot);
        $arraySecondPartAmountAllIn = array(
            'Currency' => $data->currency->alphacode,
            'From' => $data->contract->validity,
            'Until' => $data->contract->expire,

        );
        $arrayCompleteAmountAllIn = array_merge($arrayFirstPartAmountAllIn, $arraySecondPartAmountAllIn);
        return $arrayCompleteAmountAllIn;

    }

    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }
    public function perTeu($monto, $calculation_type, $code)
    {
        $arrayTeu = CalculationType::where('options->isteu', true)->pluck('id')->toArray();
        $codeArray = Container::where('code', 'like', '20%')->pluck('code')->toArray();

        if (!in_array($code, $codeArray)) {
            if (in_array($calculation_type, $arrayTeu)) {
                $monto = $monto * 2;
                return $monto;
            } else {
                return $monto;
            }
        } else {
            return $monto;
        }
    }
}
