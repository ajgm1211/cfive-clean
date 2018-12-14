<?php

namespace App\Exports;

use App\Quote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuotesExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Id',
            'Owner',
            'Company user id',
            'Company quote',
            'Incoterm',
            'Validity',
            'Since validity',
            'Modality',
            'Pick up date',
            'Cargo type',
            'Origin address',
            'Destination address',
            'Company id',
            'Origin harbor id',
            'Destination harbor id',
            'Origin airport id',
            'Destination airport id',
            'Price id',
            'Contact id',
            'Currency id',
            'Carrier id',
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
            'Payment conditiones',
            'Total quantity',
            'Total weight',
            'Total volume',
            'Chargeable weight',
            'Carrier visibility',
            'Delivery type',
            'Sub total origin',
            'Sub total freight',
            'Sub total destination',
            'Total markup origin',
            'Total markup freight',
            'Total markup destination',
            'Status',
            'Terms',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Quote::all();
    }
}
