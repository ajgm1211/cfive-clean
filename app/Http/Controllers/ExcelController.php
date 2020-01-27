<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\QuoteV2;
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
        $sheet->setCellValue('B10', 'Kind of cargo: ');
        $sheet->setCellValue('C10', $quote->kind_of_cargo);
        $sheet->setCellValue('B11', 'Owner: ');
        $sheet->setCellValue('C11', $quote->user->name);
        $sheet->setCellValue('B12', 'Price Level: ');
        $sheet->setCellValue('C12', @$quote->price->name);

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
        $sheet->setCellValue('F8', 'Equipments: ');
        $sheet->setCellValue('G8', $quote->equipment);
        $sheet->setCellValue('F9', 'Incoterm: ');
        $sheet->setCellValue('G9', $quote->incoterm->name);
        $sheet->setCellValue('F10', 'Commodity: ');
        $sheet->setCellValue('G10', $quote->commodity);
        $sheet->setCellValue('F11', 'Company: ');
        $sheet->setCellValue('G11', $quote->company->business_name);
        $sheet->setCellValue('F12', 'Contact: ');
        $sheet->setCellValue('G12', $quote->contact->first_name);

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
