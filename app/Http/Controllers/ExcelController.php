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

        $sheetIndex = $spreadsheet->getIndex(
            $spreadsheet->getSheetByName('Worksheet')
        );

        $spreadsheet->removeSheetByIndex($sheetIndex);

        foreach($rates as $key=>$item){
            // Create a new worksheet called "My Data"
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $item->carrier->name);

            // Attach the "My Data" worksheet as the first worksheet in the Spreadsheet object
            $spreadsheet->addSheet($myWorkSheet);

            $sheet = $spreadsheet->getSheet($key);

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ];

            $spreadsheet->getSheet($key)->getColumnDimension('A')->setWidth(3);
            $spreadsheet->getSheet($key)->getColumnDimension('B')->setWidth(17);
            $spreadsheet->getSheet($key)->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getSheet($key)->getColumnDimension('F')->setWidth(15);
            $spreadsheet->getSheet($key)->getColumnDimension('G')->setWidth(14);
            $spreadsheet->getSheet($key)->getColumnDimension('L')->setWidth(12);

            $sheet->setCellValue('D5', 'CARTA DE INSTRUCCIONES INTERNA A OPERACIONES');
            $sheet->setCellValue('E7', 'EMBARQUE MARÍTIMO');

            $sheet->setCellValue('A9', '1)');
            $sheet->setCellValue('B9', 'Agente Corresponsal:');
            $sheet->setCellValue('C9', @$quote->company->business_name);
            $sheet->setCellValue('B10', 'Embarcador:');
            $sheet->setCellValue('C10', '');
            $sheet->setCellValue('B11', 'Consignatario:');
            $sheet->setCellValue('C11', '');

            $sheet->setCellValue('H9', 'Fecha:');
            $sheet->setCellValue('I9', date('d-m-Y', strtotime($quote->date_issued)));
            $sheet->setCellValue('H10', 'Referencia:');
            $sheet->setCellValue('I10', $quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id);

            $sheet->setCellValue('A13', '2)');
            $sheet->setCellValue('B13', 'Crédito:');

            $sheet->setCellValue('A15', '3)');
            $sheet->setCellValue('B15', 'Tipo de carga:');
            $sheet->setCellValue('C15', 'Carga General');

            $sheet->setCellValue('B16', 'Incoterm:');
            $sheet->setCellValue('C16', $quote->incoterm->name);

            $sheet->setCellValue('G15', 'Embarque:');
            $sheet->setCellValue('H15', $quote->type);

            $sheet->setCellValue('G16', 'Mercancía:');
            $sheet->setCellValue('H16', '');
            $sheet->setCellValue('G17', 'Tipo de CNTR:');
            $sheet->setCellValue('H17', $quote->equipment);
            $sheet->setCellValue('G18', 'Otro:');

            $sheet->setCellValue('A19', '4)');
            $sheet->setCellValue('B19', 'Cantidad:');
            $sheet->setCellValue('C19', $quote->type=='FCL' ? 'N/A':$quote->	total_quantity);

            $sheet->setCellValue('B20', 'Dimensiones:');
            $sheet->setCellValue('C20', $quote->type=='FCL' ? 'N/A':'');

            $sheet->setCellValue('B21', 'Peso Bruto:');
            $sheet->setCellValue('C21', $quote->type=='FCL' ? 'N/A':$quote->total_weight);

            $sheet->setCellValue('B22', 'Volumen:');
            $sheet->setCellValue('C22', $quote->type=='FCL' ? 'N/A':$quote->total_volume);

            $sheet->setCellValue('A24', '7)');
            $sheet->setCellValue('B24', 'POL:');
            $sheet->setCellValue('C24', $item->origin_port->name.', '.$item->origin_port->code);

            $sheet->setCellValue('B25', 'POD:');
            $sheet->setCellValue('C25', $item->destination_port->name.', '.$item->destination_port->code);

            $sheet->setCellValue('A27', '8)');
            $sheet->setCellValue('B27', 'Dirección de recolección:');
            $sheet->setCellValue('C27', '');

            $sheet->setCellValue('B28', 'Dirección de entrega:');
            $sheet->setCellValue('C28', '');

            $sheet->setCellValue('B29', 'Proveedor terrestre:');
            $sheet->setCellValue('C29', '');

            $sheet->setCellValue('A31', '9)');
            $sheet->setCellValue('B31', 'Naviera/co-loader:');
            $sheet->setCellValue('C31', $item->carrier->name);
            $sheet->setCellValue('B32', 'Agente Aduanal:');
            $sheet->setCellValue('C32', 'Del consignatario');
            $sheet->setCellValue('B33', 'Incluye pistas:');
            $sheet->setCellValue('C33', '');
            $sheet->setCellValue('B34', 'Incluye THC:');
            $sheet->setCellValue('C34', '');

            $sheet->setCellValue('G20', '5) Requiere seguro:');
            $sheet->setCellValue('H21', '');

            $sheet->setCellValue('G22', '6) Carga peligrosa:');
            $sheet->setCellValue('H22', '');

            $sheet->setCellValue('G31', 'Proveedor de traslado a báscula:');
            $sheet->setCellValue('H31', 'N/A');
            $sheet->setCellValue('G32', 'Báscula para pesaje:');
            $sheet->setCellValue('H32', 'N/A');

            $sheet->setCellValue('A36', '10)');
            $sheet->setCellValue('B36', 'Expediente Fiscal:');
            $sheet->setCellValue('C36', '');

            $sheet->setCellValue('A38', '11)');
            $sheet->setCellValue('B38', 'Cargos Adicionales:');
            $sheet->setCellValue('C38', '');

            //Table
            $sheet->setCellValue('E40', 'DESGLOSE DE CARGOS');

            $sheet->setCellValue('B41', 'ID');
            $sheet->setCellValue('C41', 'Concepto');
            $sheet->setCellValue('D41', 'Costo');
            $sheet->setCellValue('F41', 'Moneda');
            $sheet->setCellValue('E41', 'Unidad');
            $sheet->setCellValue('G41', 'Venta');
            $sheet->setCellValue('H41', 'Moneda');
            $sheet->setCellValue('I41', 'Unidad');
            $sheet->setCellValue('J41', 'CC/PP');

            if($quote->type=='LCL' || $quote->type=='AIR'){
                $i=42;
                foreach($item->charge_lcl_air as $charge){
                    $sheet->setCellValue('B'.$i, $charge->id);
                    $sheet->setCellValue('C'.$i, @$charge->surcharge->name);
                    $sheet->setCellValue('D'.$i, $charge->price_per_unit);
                    $sheet->setCellValue('D'.$i, $charge->price_per_unit);
                    $i++;
                }
            }

            $spreadsheet->getSheet($key)->getStyle('A1:AU200')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFFF');

            $spreadsheet->getSheet($key)->getStyle('A5:J5')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E13B24');

            $spreadsheet->getSheet($key)->getStyle('B40:J40')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E13B24');

            $spreadsheet->getSheet($key)->getStyle('A5:P5')->getFont()->setBold( true );
            $spreadsheet->getSheet($key)->getStyle('A7:P7')->getFont()->setBold( true );
            $spreadsheet->getSheet($key)->getStyle('B40:P40')->getFont()->setBold( true );

            $spreadsheet->getSheet($key)->getStyle('A5:P5')->getFont()->getColor()->setARGB('FFFFFF');
            $spreadsheet->getSheet($key)->getStyle('B40:N40')->getFont()->getColor()->setARGB('FFFFFF');

            $i = $i-1;
            for ($i; $i > 40; $i--) { 
                $spreadsheet->getSheet($key)->getStyle('B'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('C'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('D'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('E'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('F'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('G'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('H'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('I'.$i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('J'.$i)->applyFromArray($styleArray);
            }
        }

        //Bottom border
        $spreadsheet->getSheet($key)->getStyle('C9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C11')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C13')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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
