<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class AutomaticInlandFilter extends AbstractFilter
{
    protected $paginate = 100;
    protected $filter_by = [];
    protected $filter_by_relations = [];
    protected $with = ['quote',
                        'port', 
                        'currency'
                        ];

}