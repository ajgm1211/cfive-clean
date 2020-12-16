<?php

namespace App\Http\Filters;

use App\Http\Filters\AbstractFilter;

class QuotationFilter extends AbstractFilter
{
    protected $paginate = 10;
    protected $filter_by = [ 'quote_id'];
    protected $filter_by_relations = [ 'origin_harbor__display_name', 'destination_harbor__display_name', 'company__business_name', 'user__name'];
    protected $with = [];

}
