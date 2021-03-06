<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class ChargeFilter extends AbstractFilter
{
    protected $paginate = 100;
    protected $filter_by = [];
    protected $filter_by_relations = [];
    protected $with = ['automatic_rate', 
                    'type',
                    'calculation_type',
                    'currency',
                    ];

}