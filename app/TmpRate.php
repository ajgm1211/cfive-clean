<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TmpRate extends Model
{
    protected $table = 'tmp_rates';
    protected $fillable = ['PortOrigin',
                           'PortDestination',
                           'Carrier',
                           'Rate20',
                           'Rate40',
                           'Rate40HC',
                           'Currency'
                          ];
}
