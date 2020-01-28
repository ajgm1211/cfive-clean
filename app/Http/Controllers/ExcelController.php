<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\QuoteV2;
use App\AutomaticRate;
use App\Http\Traits\QuoteV2Trait;

class ExcelController extends Controller
{
    use QuoteV2Trait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function costPageQuote($id)
    {
        $id = obtenerRouteKey($id);

        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge','automaticInlandLclAir','charge_lcl_air')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000'],
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $spreadsheet->getActiveSheet()->getStyle('B19:H19')->getAlignment()
            ->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('G11')->getAlignment()
            ->setHorizontal('left');
        $spreadsheet->getActiveSheet()->getStyle('C15')->getAlignment()
            ->setHorizontal('left');

        //Add Logo
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');

        $drawing->setPath('./images/logo.png');

        $drawing->setCoordinates('B2');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $sheet->setCellValue('E2', $quote->custom_quote_id!='' ? 'COTIZACIÓN '.$quote->custom_quote_id:'COTIZACIÓN '.$quote->quote_id);
        $sheet->setCellValue('H2', $quote->created_at);

        $validity = $quote->validity_start ." / ". $quote->validity_end;

        $sheet->setCellValue('B5', 'Type: ');
        $sheet->setCellValue('C5', $quote->type);
        $sheet->setCellValue('B6', 'Quotation ID: ');
        $sheet->setCellValue('C6', $quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id);
        $sheet->setCellValue('B7', 'Status: ');
        $sheet->setCellValue('C7', $quote->status);
        $sheet->setCellValue('B8', 'Date issued: ');
        $sheet->setCellValue('C8', $quote->date_issued);
        $sheet->setCellValue('B9', 'Validity: ');
        $sheet->setCellValue('C9', $validity);
        $sheet->setCellValue('B10', 'Owner: ');
        $sheet->setCellValue('C10', $quote->user->name.' '.$quote->user->lastname);
        $sheet->setCellValue('B11', 'Equipments: ');
        $sheet->setCellValue('C11', $quote->equipment);
        $sheet->setCellValue('B12', 'Incoterm: ');
        $sheet->setCellValue('C12', $quote->incoterm->name);


        $sheet->setCellValue('F5', 'Destination type: ');
        if($quote->type=='AIR'){
            switch($quote->delivery_type){
                case 1:
                    $destination_type = 'Airport to Airport' ;
                    break;
                case 2:
                    $destination_type = 'Airport to Door' ;
                    break;
                case 3:
                    $destination_type = 'Door to Airport' ;
                    break;
                case 4:
                    $destination_type = 'Door to Door' ;
                    break;
            } 
        }else{
            switch($quote->delivery_type){
                case 1:
                    $destination_type = 'Port to Port' ;
                    break;
                case 2:
                    $destination_type = 'Port to Door' ;
                    break;
                case 3:
                    $destination_type = 'Door to Port' ;
                    break;
                case 4:
                    $destination_type = 'Door to Door' ;
                    break;
            }
        }

        $sheet->setCellValue('G5', $destination_type);
        $sheet->setCellValue('F6', 'Origin Address: ');
        $sheet->setCellValue('G6', $quote->origin_address);
        $sheet->setCellValue('F7', 'Destination Address: ');
        $sheet->setCellValue('G7', $quote->destination_address);
        $sheet->setCellValue('F8', 'Company: ');
        $sheet->setCellValue('G8', $quote->company->business_name);
        $sheet->setCellValue('F9', 'Contact: ');
        $sheet->setCellValue('G9', $quote->contact->first_name);
        $sheet->setCellValue('F10', 'Commodity: ');
        $sheet->setCellValue('G10', $quote->commodity);
        $sheet->setCellValue('F11', 'Kind of cargo: ');
        $sheet->setCellValue('G11', $quote->kind_of_cargo);
        $sheet->setCellValue('F12', 'Price Level: ');
        $sheet->setCellValue('G12', @$quote->price->name);

        $equipmentHides = $this->hideContainer($quote->equipment,'BD');

        $i=15;
        foreach($rates as $rate){

            $sheet->setCellValue('B'.$i, 'POL: '.$rate->origin_port->name.', '.$rate->origin_port->code);
            $sheet->setCellValue('C'.$i, 'POD: '.$rate->destination_port->name.', '.$rate->destination_port->code);
            $sheet->setCellValue('D'.$i, 'Contract: '.$rate->contract);
            $sheet->setCellValue('E'.$i, 'Type: '.$rate->schedule_type);
            $sheet->setCellValue('F'.$i, 'TT: '.$rate->transit_time);
            $sheet->setCellValue('G'.$i, 'Via: '.$rate->via);

            $sheet->setCellValue('B17', 'Freight Charges');
            $sheet->setCellValue('B19', 'Charge');
            $sheet->setCellValue('C19', 'Detail');
            $sheet->setCellValue('D19', '20\'');
            $sheet->setCellValue('E19', '40\'');
            $sheet->setCellValue('F19', '40HC\'');
            $sheet->setCellValue('G19', '40NOR\'');
            $sheet->setCellValue('H19', '45\'');

            $a=20;
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;

            $sum_m20=0;
            $sum_m40=0;
            $sum_m40hc=0;
            $sum_m40nor=0;
            $sum_m45=0;
            foreach($rate->charge as $item){
                if($item->type_id==3){
                    $rate_id=$item->automatic_rate_id;

                    $freight_amounts = json_decode($item->amount,true);
                    $freight_markups = json_decode($item->markups,true);

                    if(isset($freight_amounts['c20'])){
                        $sum20+=$item->total_20;
                    }
                    if(isset($freight_amounts['c40'])){
                        $sum40+=@$item->total_40;
                    }
                    if(isset($freight_amounts['c40hc'])){
                        $sum40hc+=@$item->total_40hc;
                    }
                    if(isset($freight_amounts['c40nor'])){
                        $sum40nor+=@$item->total_40nor;
                    }
                    if(isset($freight_amounts['c45'])){
                        $sum45+=@$item->total_45;
                    }

                    if(isset($freight_markups['m20'])){
                        $sum_m20+=$item->total_markup20;
                    }
                    if(isset($freight_markups['m40'])){
                        $sum_m40+=@$item->total_markup40;
                    }
                    if(isset($freight_markups['m40hc'])){
                        $sum_m40hc+=@$item->total_markup40hc;
                    }
                    if(isset($freight_markups['m40nor'])){
                        $sum_m40nor+=@$item->total_markup40nor;
                    }
                    if(isset($freight_markups['m45'])){
                        $sum_m45+=@$item->total_markup45;
                    }

                    $sheet->setCellValue('B'.$a, $item->surcharge_id !='' ? $item->surcharge_id:'Ocean Freight');
                    $sheet->setCellValue('C'.$a, $item->surcharge_id !='' ? $item->surcharge_id:'Per Container');
                    $sheet->setCellValue('D'.$a, @$freight_amounts['c20']+@$freight_markups['m20']);
                    $sheet->setCellValue('E'.$a, @$freight_amounts['c40']+@$freight_markups['m40']);
                    $sheet->setCellValue('F'.$a, @$freight_amounts['c40hc']+@$freight_markups['m40hc']);
                    $sheet->setCellValue('G'.$a, @$freight_amounts['c40nor']+@$freight_markups['m40nor']);
                    $sheet->setCellValue('H'.$a, @$freight_amounts['c45']+@$freight_markups['m45']);
                }
                $a++;
            }

            $i++;
        }
        
        $end = $a+1;
        
        $spreadsheet->getActiveSheet()->getStyle('B2:I'.$end)->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getStyle('B2:I'.$end)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF');

        $spreadsheet->getActiveSheet()->getStyle('B19:H19')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('D5D5D5');

        $writer = new Xlsx($spreadsheet);
        if($quote->custom_quote_id!=''){
            $name = $quote->custom_quote_id; 
        }else{
            $name = $quote->quote_id;
        }

        $writer->save(storage_path('app/public/'.$name.'.xlsx'));

        return response()->download(storage_path('app/public/'.$name.'.xlsx'))->deleteFileAfterSend();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
