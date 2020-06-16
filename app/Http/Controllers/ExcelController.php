<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\QuoteV2;
use App\AutomaticRate;
use App\CompanyUser;
use App\Currency;
use App\Container;
use App\SaleTermV2;
use App\Harbor;
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
        $quote = QuoteV2::whereHas('rates_v2', function ($query) use ($id) {
            $query->where('id', $id);
        })->with(['rates_v2' => function ($q) use ($id) {
            $q->where('id', $id);
        }])->first();

        $sale_terms_origin = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Origin')->with('charge')->get();

        $sale_terms_destination = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Destination')->with('charge')->get();

        $containers = Container::all();

        $company_user = CompanyUser::find(\Auth::user()->company_user_id);

        $sale_terms_origin = $this->processSaleTerms($sale_terms_origin, $quote, $company_user, 'origin');

        $sale_terms_destination = $this->processSaleTerms($sale_terms_destination, $quote, $company_user, 'destination');

        $origin_sales = $sale_terms_origin->map(function ($origin) {
            return collect($origin->toArray())
                ->only(['port_id'])
                ->all();
        });

        $destination_sales = $sale_terms_destination->map(function ($origin) {
            return collect($origin->toArray())
                ->only(['port_id'])
                ->all();
        });

        $equipmentHides = $this->hideContainerV2($quote->equipment, 'BD', $containers);

        $spreadsheet = new Spreadsheet();

        $sheetIndex = $spreadsheet->getIndex(
            $spreadsheet->getSheetByName('Worksheet')
        );

        $spreadsheet->removeSheetByIndex($sheetIndex);

        $rates = $quote->rates_v2;

        $carrier = null;

        foreach ($rates as $key => $item) {
            $carrier = @$item->carrier->name;
            // Create a new worksheet called "My Data"
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Data');

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
            $spreadsheet->getSheet($key)->getColumnDimension('B')->setWidth(20);
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

            $sheet->setCellValue('G9', 'Fecha:');
            $sheet->setCellValue('H9', date('d-m-Y', strtotime($quote->date_issued)));
            $sheet->setCellValue('G10', 'Referencia:');
            $sheet->setCellValue('H10', $quote->custom_quote_id != '' ? $quote->custom_quote_id : $quote->quote_id);

            $sheet->setCellValue('A13', '2)');
            $sheet->setCellValue('B13', 'Crédito:');

            $sheet->setCellValue('A15', '3)');
            $sheet->setCellValue('B15', 'Tipo de carga:');
            $sheet->setCellValue('C15', 'Carga General');

            $sheet->setCellValue('B16', 'Incoterm:');
            $sheet->setCellValue('C16', @$quote->incoterm->name);

            $sheet->setCellValue('G15', 'Embarque:');
            $sheet->setCellValue('H15', $quote->type);

            //Set equipments
            $json_equipment = json_decode($quote->equipment);
            $str = null;
            foreach ($equipmentHides as $k => $hide) {
                if ($hide != 'hidden') {
                    foreach ($containers as $c) {
                        if ($c->code == $k) {
                            $str .= $k . ' ';
                        }
                    }
                }
            }

            $sheet->setCellValue('G16', 'Mercancía:');
            $sheet->setCellValue('H16', '');
            $sheet->setCellValue('G17', 'Tipo de CNTR:');
            $sheet->setCellValue('H17', $quote->type == 'FCL' ? $str : 'N/A');
            $sheet->setCellValue('G18', 'Otro:');

            $sheet->setCellValue('A19', '4)');
            $sheet->setCellValue('B19', 'Cantidad:');
            $sheet->setCellValue('C19', $quote->type == 'FCL' ? 'N/A' : $quote->total_quantity);

            $sheet->setCellValue('B20', 'Dimensiones:');
            $sheet->setCellValue('C20', $quote->type == 'FCL' ? 'N/A' : @$quote->packing_load->height . ' x ' . @$quote->packing_load->width . ' x ' . @$quote->packing_load->large);

            $sheet->setCellValue('B21', 'Peso Bruto:');
            $sheet->setCellValue('C21', $quote->type == 'FCL' ? 'N/A' : $quote->total_weight . ' Kg');

            $sheet->setCellValue('B22', 'Volumen:');
            $sheet->setCellValue('C22', $quote->type == 'FCL' ? 'N/A' : $quote->total_volume . ' m3');

            $sheet->setCellValue('A24', '7)');
            $sheet->setCellValue('B24', 'POL:');
            $sheet->setCellValue('C24', $item->origin_port->name . ', ' . $item->origin_port->code);

            $sheet->setCellValue('B25', 'POD:');
            $sheet->setCellValue('C25', $item->destination_port->name . ', ' . $item->destination_port->code);

            $sheet->setCellValue('A27', '8)');
            $sheet->setCellValue('B27', 'Dirección de recolección:');
            $sheet->setCellValue('C27', $quote->origin_address);

            $sheet->setCellValue('B28', 'Dirección de entrega:');
            $sheet->setCellValue('C28', $quote->destination_address);

            $sheet->setCellValue('B29', 'Proveedor terrestre:');
            $sheet->setCellValue('C29', '');

            $sheet->setCellValue('A31', '9)');
            $sheet->setCellValue('B31', 'Naviera/co-loader:');
            $sheet->setCellValue('C31', @$item->carrier->name);
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

            $sheet->setCellValue('G23', 'UN:');
            $sheet->setCellValue('H23', '');

            $sheet->setCellValue('G24', 'Clase:');
            $sheet->setCellValue('H24', '');

            $sheet->setCellValue('I24', 'PG:');
            $sheet->setCellValue('J24', '');

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
            $sheet->setCellValue('B40', 'DESGLOSE DE CARGOS');

            $sheet->setCellValue('B41', 'Cargo');
            $sheet->setCellValue('C41', 'Detalle');
            if ($quote->type == 'FCL') {
                $col = 'D';
                $spreadsheet->getSheet($key)->getStyle($col . '41')->applyFromArray($styleArray);
                foreach ($equipmentHides as $k => $hide) {
                    foreach ($containers as $c) {
                        if ($c->code == $k && $hide == "") {
                            $sheet->setCellValue($col++ . '41', $k);
                            $spreadsheet->getSheet($key)->getStyle($col . '41')->applyFromArray($styleArray);
                        }
                    }
                }
                $sheet->setCellValue($col . '41', 'Moneda');
            } else {
                $sheet->setCellValue('D41', 'Unidades');
                $sheet->setCellValue('E41', 'Precio');
                $sheet->setCellValue('F41', 'Total');
                $sheet->setCellValue('G41', 'Moneda');
                $spreadsheet->getSheet($key)->getStyle('D41')->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('E41')->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('F41')->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('G41')->applyFromArray($styleArray);
            }

            $i = 42;
            
            if ($quote->type == 'LCL' || $quote->type == 'AIR') {
                foreach ($item->charge_lcl_air as $charge) {
                    $type = $this->getAmountType($charge->type_id);
                    $sheet->setCellValue('B' . $i, $type);
                    if ($charge->surcharge_id == '') {
                        $sheet->setCellValue('C' . $i, 'Ocean freight');
                    } else {
                        $sheet->setCellValue('C' . $i, @$charge->surcharge->name);
                    }
                    $sheet->setCellValue('D' . $i, $charge->units);
                    $sheet->setCellValue('E' . $i, (($charge->units * $charge->price_per_unit) + $charge->markup) / $charge->units);
                    $sheet->setCellValue('F' . $i, ($charge->units * $charge->price_per_unit) + $charge->markup);
                    $sheet->setCellValue('G' . $i, $charge->currency->alphacode);
                    $spreadsheet->getSheet($key)->getStyle('D' . $i)->applyFromArray($styleArray);
                    $spreadsheet->getSheet($key)->getStyle('E' . $i)->applyFromArray($styleArray);
                    $spreadsheet->getSheet($key)->getStyle('F' . $i)->applyFromArray($styleArray);
                    $spreadsheet->getSheet($key)->getStyle('G' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            } else {

                $this->calculateFcl($item, $containers);
                
                foreach ($item->charge as $charge) {

                    $col_amount = 'D';
                    $spreadsheet->getSheet($key)->getStyle($col_amount . $i)->applyFromArray($styleArray);

                    foreach ($containers as $c) {
                        ${'sum_' . $c->code} = 0;
                        ${'sum_m' . $c->code} = 0;
                        $total = 0;
                        $total_m = 0;
                    }

                    $amounts = json_decode($charge->amount, true);
                    $markups = json_decode($charge->markups, true);

                    $amounts = $this->processOldContainers($amounts, 'amounts');
                    $markups = $this->processOldContainers($markups, 'markups');

                    foreach ($containers as $c) {
                        if (isset($amounts['c' . $c->code])) {
                            ${'sum_' . $c->code} += @$charge->${'total_' . $c->code};
                            $total += ${'sum_' . $c->code};
                        }

                        if (isset($markups['m' . $c->code])) {
                            ${'sum_m' . $c->code} += @$charge->${'total_' . $c->code};
                            $total_m += ${'sum_m' . $c->code};
                        }
                    }

                    $type = $this->getAmountType($charge->type_id);
                    if ($charge->surcharge_id == '') {
                        $sheet->setCellValue('B' . $i, 'Ocean freight');
                    } else {
                        $sheet->setCellValue('B' . $i, @$charge->surcharge->name);
                    }
                    $sheet->setCellValue('C' . $i, @$charge->calculation_type->name);
                    foreach ($equipmentHides as $k => $hide) {
                        foreach ($containers as $c) {
                            if ($c->code == $k && $hide == "") {
                                $sheet->setCellValue($col_amount++ . $i, @$amounts['c' . $c->code] + @$markups['m' . $c->code]);
                                $spreadsheet->getSheet($key)->getStyle($col_amount . $i)->applyFromArray($styleArray);
                            }
                        }
                    }
                    $sheet->setCellValue($col_amount . $i, $charge->currency->alphacode);
                    $spreadsheet->getSheet($key)->getStyle($col_amount . $i)->applyFromArray($styleArray);

                    $i++;
                }

                foreach ($item->inland as $inland) {
                    $col_inland = 'D';
                    $spreadsheet->getSheet($key)->getStyle($col_inland . $i)->applyFromArray($styleArray);

                    $inland_rates = json_decode($inland->rate, true);
                    $inland_markups = json_decode($inland->markup, true);

                    $inland_rates = $this->processOldContainers($inland_rates, 'amounts');
                    $inland_markups = $this->processOldContainers($inland_markups, 'markups');

                    $sheet->setCellValue('B' . $i, $inland->provider);
                    $sheet->setCellValue('C' . $i, $inland->distance.' Km');
                    foreach ($equipmentHides as $k => $hide) {
                        foreach ($containers as $c) {
                            if ($c->code == $k && $hide == "") {
                                $sheet->setCellValue($col_inland++ . $i, @$inland_rates['c' . $c->code] + @$inland_markups['m' . $c->code]);
                                $spreadsheet->getSheet($key)->getStyle($col_inland . $i)->applyFromArray($styleArray);
                            }
                        }
                    }
                    $sheet->setCellValue($col_inland . $i, $inland->currency->alphacode);

                    $i++;
                }

                $check_port_origin = $this->find_key_value($origin_sales->toArray(), 'port_id', $item->origin_port_id);

                if ($check_port_origin) {
                    foreach ($sale_terms_origin as $sale_term_origin) {
                        foreach ($sale_term_origin->charge as $sale_origin) {
                            $col_sale = 'D';
                            $spreadsheet->getSheet($key)->getStyle($col_sale . $i)->applyFromArray($styleArray);

                            $sale_rates = json_decode($sale_origin->rate, true);

                            $sheet->setCellValue('B' . $i, $sale_origin->charge);
                            $sheet->setCellValue('C' . $i, $sale_origin->detail);
                            foreach ($equipmentHides as $k => $hide) {
                                foreach ($containers as $c) {
                                    if ($c->code == $k && $hide == "") {
                                        $sheet->setCellValue($col_sale++ . $i, @$sale_rates['c' . $c->code]);
                                        $spreadsheet->getSheet($key)->getStyle($col_sale . $i)->applyFromArray($styleArray);
                                    }
                                }
                            }
                            $sheet->setCellValue($col_sale . $i, @$sale_origin->currency->alphacode);

                            $i++;
                        }
                    }
                }

                $check_port_destination = $this->find_key_value($destination_sales->toArray(), 'port_id', $item->destination_port_id);
                
                if($check_port_destination){
                    foreach ($sale_terms_destination as $sale_term_destination) {
                        foreach ($sale_term_destination->charge as $sale_destination) {
                            $col_sale = 'D';
                            $spreadsheet->getSheet($key)->getStyle($col_sale . $i)->applyFromArray($styleArray);

                            $sale_rates = json_decode($sale_destination->rate, true);

                            $sheet->setCellValue('B' . $i, $sale_destination->charge);
                            $sheet->setCellValue('C' . $i, $sale_destination->detail);
                            foreach ($equipmentHides as $k => $hide) {
                                foreach ($containers as $c) {
                                    if ($c->code == $k && $hide == "") {
                                        $sheet->setCellValue($col_sale++ . $i, @$sale_rates['c' . $c->code]);
                                        $spreadsheet->getSheet($key)->getStyle($col_sale . $i)->applyFromArray($styleArray);
                                    }
                                }
                            }
                            $sheet->setCellValue($col_sale . $i, @$sale_destination->currency->alphacode);

                            $i++;
                        }
                    }
                }
            }

            $spreadsheet->getSheet($key)->getStyle('A1:AU200')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFFFF');

            $spreadsheet->getSheet($key)->getStyle('A5:J5')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E13B24');

            $spreadsheet->getSheet($key)->getStyle('B40')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('E13B24');

            $spreadsheet->getSheet($key)->getStyle('A5:P5')->getFont()->setBold(true);
            $spreadsheet->getSheet($key)->getStyle('A7:P7')->getFont()->setBold(true);
            $spreadsheet->getSheet($key)->getStyle('B40:P40')->getFont()->setBold(true);
            $spreadsheet->getSheet($key)->getStyle('B41:P41')->getFont()->setBold(true);

            $spreadsheet->getSheet($key)->getStyle('A5:P5')->getFont()->getColor()->setARGB('FFFFFF');
            $spreadsheet->getSheet($key)->getStyle('B40:N40')->getFont()->getColor()->setARGB('FFFFFF');

            $i = $i - 1;
            $a = $i + 2;

            for ($i; $i > 40; $i--) {
                $spreadsheet->getSheet($key)->getStyle('B' . $i)->applyFromArray($styleArray);
                $spreadsheet->getSheet($key)->getStyle('C' . $i)->applyFromArray($styleArray);
            }
        }

        $b = $a + 1;
        $c = $b + 2;
        $d = $c + 1;
        $e = $d + 2;
        $f = $e + 1;

        $sheet->setCellValue('A' . $a, '12)');
        $sheet->setCellValue('B' . $a, 'Vigencia de tarifa:');
        $sheet->setCellValue('C' . $a, $quote->validity_start . '/' . $quote->validity_end);
        $spreadsheet->getSheet($key)->getStyle('C' . $a)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue('B' . $b, 'Enviar pre-alerta a:');
        $sheet->setCellValue('C' . $b, @$quote->company->business_name);
        $spreadsheet->getSheet($key)->getStyle('C' . $b)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue('A' . $c, '13)');
        $sheet->setCellValue('B' . $c, 'Instrucciones adicionales:');
        $sheet->setCellValue('C' . $c, '');
        $spreadsheet->getSheet($key)->getStyle('C' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('D' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('E' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('F' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('G' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('J' . $c)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue('B' . $d, 'Se otorgó beneficio de carta garantía:');
        $sheet->setCellValue('C' . $d, '');
        $spreadsheet->getSheet($key)->getStyle('C' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('D' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('E' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('F' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('G' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('J' . $d)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue('A' . $e, '14)');
        $sheet->setCellValue('B' . $e, 'Vendedor:');
        $sheet->setCellValue('C' . $e, $quote->user->name . ' ' . $quote->user->lastname);
        $spreadsheet->getSheet($key)->getStyle('C' . $e)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->setCellValue('B' . $f, 'Elaboró:');
        $sheet->setCellValue('C' . $f, '');
        $spreadsheet->getSheet($key)->getStyle('C' . $f)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        //Bottom border
        $spreadsheet->getSheet($key)->getStyle('C9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C11')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C13')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C15')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C16')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C19')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C20')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C21')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C22')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C24')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C25')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C27')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C28')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C29')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C31')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C32')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C33')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C34')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C36')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('C38')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('E9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('E10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('E11')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('D9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('D10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('D11')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('H9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H15')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H16')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H17')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H18')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H20')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H22')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H23')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H24')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H31')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('H32')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('I9')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I10')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I15')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I16')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I17')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I18')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I20')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I22')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I23')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I31')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getSheet($key)->getStyle('I32')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('J24')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getSheet($key)->getStyle('E7')->getFont()->setUnderline(true);

        $writer = new Xlsx($spreadsheet);
        if ($quote->custom_quote_id != '') {
            $name = $quote->custom_quote_id;
        } else {
            $name = $quote->quote_id;
        }

        $writer->save(storage_path('app/public/' . $name . '-' . $carrier . '.xlsx'));

        return response()->download(storage_path('app/public/' . $name . '-' . $carrier . '.xlsx'))->deleteFileAfterSend();
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

    public function calculateFcl($rates, $containers)
    {
        $sum = 'sum';
        $markup = 'markup';
        $inland = 'inland';
        $amount = 'amount';
        $total = 'total';

        foreach ($containers as $c) {
            ${$sum . '_' . $c->code} = 0;
            ${$total . '_' . $c->code} = 0;
            ${$total . '_markup_' . $c->code} = 0;
        }

        //Charges
        foreach ($rates->charge as $value) {

            $company = CompanyUser::find(\Auth::user()->company_user_id);

            $typeCurrency =  $company->currency->alphacode;

            $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

            $array_amounts = json_decode($value->amount, true);
            $array_markups = json_decode($value->markups, true);
            $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
            $array_markups = $this->processOldContainers($array_markups, 'markups');
            $pre_c = 'total_c';
            $pre_m = 'total_m';

            foreach ($containers as $c) {
                ${$pre_c . $c->code} = 'total_c' . $c->code;
                ${$pre_m . $c->code} = 'total_m' . $c->code;
                if (isset($array_amounts['c' . $c->code])) {
                    ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                    ${$amount . '_' . $total . '_' . $c->code} = ${$amount . '_' . $c->code} / $currency_rate;
                    ${$total . '_' . $c->code} = number_format(${$amount . '_' . $total . '_' . $c->code}, 2, '.', '');
                    $value->${$pre_c . $c->code} = ${$total . '_' . $c->code};
                }

                if (isset($array_markups['m' . $c->code])) {
                    ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                    ${$total . '_markup_' . $c->code} = ${$markup . '_' . $c->code} / $currency_rate;
                    $value->${$pre_m . $c->code} = ${$total . '_markup_' . $c->code};
                }
            }

            $currency_charge = Currency::find($value->currency_id);
            $value->currency_usd = $currency_charge->rates;
            $value->currency_eur = $currency_charge->rates_eur;
        }

        //Inland
        foreach ($rates->inland as $item) {
            foreach ($containers as $c) {
                ${$sum . '_' . $inland . '_' . $c->code} = 0;
                ${$total . '_' . $inland . '_' . $markup . '_' . $c->code} = 0;
            }

            $company = CompanyUser::find(\Auth::user()->company_user_id);

            $typeCurrency =  $company->currency->alphacode;

            $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

            $array_amounts = json_decode($item->rate, true);
            $array_markups = json_decode($item->markup, true);

            $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
            $array_markups = $this->processOldContainers($array_markups, 'markups');

            foreach ($containers as $c) {
                ${$sum . '_' . $total . '_' . $inland . $c->code} = 0;
                if (isset($array_amounts['c' . $c->code])) {
                    ${$amount . '_' . $inland . $c->code} = $array_amounts['c' . $c->code];
                    ${$total . '_' . $inland . $c->code} = ${$amount . '_' . $inland . $c->code} / $currency_rate;
                    ${$sum . '_' . $total . '_' . $inland . $c->code} = number_format(${$total . '_' . $inland . $c->code}, 2, '.', '');
                }
                if (isset($array_markups['m' . $c->code])) {
                    ${$markup . '_' . $inland . $c->code} = $array_markups['m' . $c->code];
                    ${$total . '_' . $inland . '_' . $markup . $c->code} = number_format(${$markup . '_' . $inland . $c->code} / $currency_rate, 2, '.', '');
                }

                $item->${$total . '_c' . $c->code} = number_format(@${$sum . '_' . $total . '_' . $inland . $c->code}, 2, '.', '');
                $item->${$total . '_m' . $c->code} = number_format(@${$total . '_' . $inland . '_' . $markup . $c->code}, 2, '.', '');
            }

            $currency_charge = Currency::find($item->currency_id);
            $item->currency_usd = $currency_charge->rates;
            $item->currency_eur = $currency_charge->rates_eur;
        }


        return $rates;
    }

    public function calculateFclOld($rates)
    {


        $sum20 = 0;
        $sum40 = 0;
        $sum40hc = 0;
        $sum40nor = 0;
        $sum45 = 0;

        $total_markup20 = 0;
        $total_markup40 = 0;
        $total_markup40hc = 0;
        $total_markup40nor = 0;
        $total_markup45 = 0;

        $total_rate20 = 0;
        $total_rate40 = 0;
        $total_rate40hc = 0;
        $total_rate40nor = 0;
        $total_rate45 = 0;

        $total_rate_markup20 = 0;
        $total_rate_markup40 = 0;
        $total_rate_markup40hc = 0;
        $total_rate_markup40nor = 0;
        $total_rate_markup45 = 0;

        $total_lcl_air_freight = 0;
        $total_lcl_air_origin = 0;
        $total_lcl_air_destination = 0;

        foreach ($rates->charge as $value) {

            $company = CompanyUser::find(\Auth::user()->company_user_id);

            $typeCurrency =  $company->currency->alphacode;

            $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

            $array_amounts = json_decode($value->amount, true);
            $array_markups = json_decode($value->markups, true);

            if (isset($array_amounts['c20'])) {
                $amount20 = $array_amounts['c20'];
                $total20 = $amount20;
                $sum20 = number_format($total20, 2, '.', '');
            }

            if (isset($array_markups['m20'])) {
                $markup20 = $array_markups['m20'];
                $total_markup20 = $markup20;
            }

            if (isset($array_amounts['c40'])) {
                $amount40 = $array_amounts['c40'];
                $total40 = $amount40;
                $sum40 = number_format($total40, 2, '.', '');
            }

            if (isset($array_markups['m40'])) {
                $markup40 = $array_markups['m40'];
                $total_markup40 = $markup40;
            }

            if (isset($array_amounts['c40hc'])) {
                $amount40hc = $array_amounts['c40hc'];
                $total40hc = $amount40hc;
                $sum40hc = number_format($total40hc, 2, '.', '');
            }

            if (isset($array_markups['m40hc'])) {
                $markup40hc = $array_markups['m40hc'];
                $total_markup40hc = $markup40hc;
            }

            if (isset($array_amounts['c40nor'])) {
                $amount40nor = $array_amounts['c40nor'];
                $total40nor = $amount40nor;
                $sum40nor = number_format($total40nor, 2, '.', '');
            }

            if (isset($array_markups['m40nor'])) {
                $markup40nor = $array_markups['m40nor'];
                $total_markup40nor = $markup40nor;
            }

            if (isset($array_amounts['c45'])) {
                $amount45 = $array_amounts['c45'];
                $total45 = $amount45;
                $sum45 = number_format($total45, 2, '.', '');
            }

            if (isset($array_markups['m45'])) {
                $markup45 = $array_markups['m45'];
                $total_markup45 = $markup45;
            }

            $value->total_20 = number_format($sum20, 2, '.', '');
            $value->total_40 = number_format($sum40, 2, '.', '');
            $value->total_40hc = number_format($sum40hc, 2, '.', '');
            $value->total_40nor = number_format($sum40nor, 2, '.', '');
            $value->total_45 = number_format($sum45, 2, '.', '');

            $value->total_markup20 = number_format($total_markup20, 2, '.', '');
            $value->total_markup40 = number_format($total_markup40, 2, '.', '');
            $value->total_markup40hc = number_format($total_markup40hc, 2, '.', '');
            $value->total_markup40nor = number_format($total_markup40nor, 2, '.', '');
            $value->total_markup45 = number_format($total_markup45, 2, '.', '');
        }

        return $value;
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

    public function getAmountType($type)
    {
        switch ($type) {
            case 1:
                return 'Origin';
                break;
            case 2:
                return 'Destination';
                break;
            case 3:
                return 'Freight';
                break;
        }
    }
}
