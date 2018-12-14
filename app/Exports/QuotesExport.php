<?php

namespace App\Exports;

use App\Quote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class QuotesExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function collection()
    {
        return Quote::all() ;

    }

    public function headings(): array
    {
        return [
            'Id',
            'Owner',
            'Company quote',
            'Incoterm',
            'Validity',
            'Since validity',
            'Modality',
            'Pick up date',
            'Delivery type',
            'Cargo type',
            'Origin',
            'Destination',
            'Origin address',
            'Destination address',
            'Company',
            'Contact',
            'Currency',
            'Carrier',
            'Container 20',
            'Container 40',
            'Container 40 HC',
            'Container 45 HC',
            'Container 40 NOR',
            'Container 20 Reefer',
            'Container 40 Reefer',
            'Container 40 HC Reefer',
            'Container 20 Open Top',
            'Container 40 Open Top',
            'PDF language',
            'Total quantity',
            'Total weight',
            'Total volume',
            'Chargeable weight',
            'Sub total origin',
            'Sub total freight',
            'Sub total destination',
            'Total markup origin',
            'Total markup freight',
            'Total markup destination',
            'Status',
            'Created at',
        ];
    }

    /**
     * @var Quote $quote
     */
    public function map($quote): array
    {
        if($quote->origin_harbor){
            $origin = $quote->origin_harbor->display_name;
        } elseif($quote->origin_airport){
            $origin = $quote->origin_airport->name;
        } else {
            $origin = $quote->origin_address;
        }

        if($quote->destination_harbor){
            $destination = $quote->destination_harbor->display_name;
        } elseif($quote->destination_airport){
            $destination = $quote->destination_airport->name;
        } else {
            $destination = $quote->destination_address;
        }

        if($quote->pdf_language==1){
            $pdf_language = 'English';
        } elseif($quote->pdf_language==2){
            $pdf_language = 'Spanish';
        } elseif($quote->pdf_language==3) {
            $pdf_language = 'Portuguese';
        }else{
            $pdf_language = 'English';
        }

        if($quote->type_cargo==1){
            $cargo_type = 'FCL';
        } elseif($quote->type_cargo==2){
            $cargo_type = 'LCL';
        } else{
            $cargo_type = 'AIR';
        }

        if($quote->delivery_type==1){
            $delivery_type = 'Port to Port';
        } elseif($quote->delivery_type==2){
            $delivery_type = 'Port to Door';
        } elseif($quote->delivery_type==3){
            $delivery_type = 'Door to Port';
        } else{
            $delivery_type = 'Door to Door';
        }

        if($quote->carrier_id!=''){
            $carrier=$quote->carrier->name;
        }else{
            $carrier=$quote->airline->name;
        }

        if($quote->modality==1){
            $modality='Export';
        }else{
            $modality='Import';
        }

        if($quote->incoterm==1){
            $incoterm='EWX';
        }elseif($quote->incoterm==2){
            $incoterm='FAS';
        }elseif($quote->incoterm==3){
            $incoterm='FCA';
        }elseif($quote->incoterm==4){
            $incoterm='FOB';
        }elseif($quote->incoterm==5){
            $incoterm='CFR';
        }elseif($quote->incoterm==6){
            $incoterm='CIF';
        }elseif($quote->incoterm==7){
            $incoterm='CIP';
        }elseif($quote->incoterm==8){
            $incoterm='DAT';
        }elseif($quote->incoterm==9){
            $incoterm='DAP';
        }elseif($quote->incoterm==10){
            $incoterm='DDP';
        }

        return [
            $quote->id,
            $quote->user->name.' '.$quote->user->lastname,
            $quote->company_quote,
            $incoterm,
            $quote->validity,
            $quote->since_validity,
            $modality,
            $quote->pick_up_date,
            $delivery_type,
            $cargo_type,
            $origin,
            $destination,
            $quote->origin_address,
            $quote->destination_address,
            $quote->company->business_name,
            $quote->contact->first_name.' '.$quote->contact->last_name,
            $quote->currencies->alphacode,
            $carrier,
            $quote->qty_20,
            $quote->qty_40,
            $quote->qty_40_hc,
            $quote->qty_45_hc,
            $quote->qty_40_nor,
            $quote->qty_20_reefer,
            $quote->qty_40_reefer,
            $quote->qty_40_hc_reefer,
            $quote->qty_20_open_top,
            $quote->qty_40_open_top,
            $pdf_language,
            $quote->total_quantity,
            $quote->total_weight,
            $quote->total_volume,
            $quote->chargeable_weight,
            $quote->sub_total_origin,
            $quote->sub_total_freight,
            $quote->sub_total_destination,
            $quote->total_markup_origin,
            $quote->total_markup_freight,
            $quote->total_markup_destination,
            $quote->status->name,
            $quote->created_at,
        ];
    }
}
