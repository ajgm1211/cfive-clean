<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class AutomaticRateFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $filter_by_relations = [];
    protected $with = ['quote',
                        'origin_port', 
                        'destination_port', 
                        'origin_airport', 
                        'destination_airport',
                        'carrier',
                        'airline',
                        'currency'
                        ];

}