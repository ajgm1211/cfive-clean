<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class InlandRangeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ 'inland_id' ];
    protected $default_filter_by = [ 'inland_id' ];
    

}

