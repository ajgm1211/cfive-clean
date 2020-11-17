<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class SaleTermCodeFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['name', 'description'];
    protected $default_filter_by = [];
    protected $filter_by_relations = [ ];
}