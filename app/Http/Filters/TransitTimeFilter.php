<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class TransitTimeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ ];
    protected $with = [ 'origin', 'destination', 'carrier', 'service' ];

}