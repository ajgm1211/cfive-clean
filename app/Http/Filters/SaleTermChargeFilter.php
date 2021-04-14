<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class SaleTermChargeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['amount'];
    protected $default_filter_by = [];
    protected $filter_by_relations = ['currency__alphacode', 'calculation_type__name', 'sale_term_code__name'];
}
