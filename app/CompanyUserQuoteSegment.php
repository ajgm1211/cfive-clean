<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Filters\CompanyUserQuoteSegmentFilter;

class CompanyUserQuoteSegment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $fillable = [
        'segment_id',
        'company_user_id', 
        'quote_segment_type_id', 
        'created_at', 
        'updated_at'
    ];
 
    public function companyUser()
    {
        return $this->belongsTo('App\CompanyUser');
    }
    public function quoteSegmentType()
    {
        return $this->belongsTo('App\QuoteSegmentType');
    }
    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;

        return $query->where('company_user_id', '=', $company_id);
    }
    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new CompanyUserQuoteSegmentFilter($request, $builder))->filter();
    }
}
