<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class SaleTermFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = ['name'];
    protected $filter_by_relations = [ 'port__display_name', 'group_container__name', 'type__name'];
    protected $default_filter_by = [];
    protected $with = ['port', 'type', 'group_container'];
}
