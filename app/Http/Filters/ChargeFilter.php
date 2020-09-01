<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ChargeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $filter_by_relations = [];
    protected $with = ['automatic_rate', 
                    'type',
                    'surcharge',
                    'calculation_type',
                    'currency',
                    ];

}