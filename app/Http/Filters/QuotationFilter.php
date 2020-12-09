<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class QuotationFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ 'quote_id'];
    protected $with = [];

}
