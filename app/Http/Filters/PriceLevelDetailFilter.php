<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class PriceLevelDetailFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $filter_by_relations = [];
    protected $with = ['price_level',
                        'currency', 
                        'price_level_apply', 
                        'direction', 
                        ];
}