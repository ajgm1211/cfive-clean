<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class OceanFreightFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [];
    protected $filter_by_relations = [ 'port_origin__display_name', 'port_destiny__display_name' ];
    protected $with = ['port_origin', 'port_destiny', 'contract', 'carrier', 'currency', 'scheduletype'];

}