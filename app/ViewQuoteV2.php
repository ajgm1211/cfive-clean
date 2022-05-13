<?php

namespace App;

use App\Http\Filters\QuotationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewQuoteV2 extends Model
{
    public function quotev2()
    {
        return $this->belongsTo('App\QuoteV2', 'quote_id');
    }

    public function scopeFilterByCurrentCompany($query)
    {
        $company_id = Auth::user()->company_user_id;
        return $query->where('company_user_id', '=', $company_id);
    }

    public function scopeFilterByCurrentUser($query)
    {
        $company_id = Auth::user()->company_user_id;
        $user_id = Auth::user()->id;
        return $query->where('company_user_id', '=', $company_id)
                     ->where('user_id', '=', $user_id);
    }
    
    public function scopeFilterByDelegation($query)
    {
        $user_id = Auth::user()->id;
        $user_delegation =UserDelegation::where('users_id','=',$user_id)->first();
        $delegation=Delegation::find($user_delegation['delegations_id']);
        $id_delegation = $delegation['id'];
        return $query->select()
                    ->join('users_delegations','view_quote_v2s.user_id','=', 'users_delegations.users_id')
                    ->where('users_delegations.delegations_id', '=', $id_delegation );
    }

    public function rates_v2()
    {
        return $this->hasMany('App\AutomaticRate', 'quote_id', 'id');
    }

    public function scopeFilter(Builder $builder, Request $request)
    {
        return (new QuotationFilter($request, $builder))->filter();
    }

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function company_user()
    {
        return $this->belongsTo('App\CompanyUser');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function origin_harbor()
    {
        return $this->hasManyThrough('App\Harbor', 'App\AutomaticRate', 'quote_id', 'id', 'id', 'origin_port_id');
    }

    public function destination_harbor()
    {
        return $this->hasManyThrough('App\Harbor', 'App\AutomaticRate', 'quote_id', 'id', 'id', 'destination_port_id');
    }

    public function status_quote()
    {
        return $this->hasOne('App\StatusQuote', 'name', 'status');
    }

    public function getContainerCodes($equip, $getGroup = false)
    {

        $size = count((array) $equip);

        if ($size != 0 && $equip != "[]") {
            $equip_array = explode(",", str_replace(["\"", "[", "]"], "", $equip));
            $equip_array = $this->validateEquipment($equip_array);
            $full_equip = "";

            foreach ($equip_array as $eq) {
                $full_equip .= Container::where('id', '=', $eq)->first()->code . ",";
                if ($getGroup) {
                    $group_id = Container::where('id', '=', $eq)->first()->gp_container_id;
                    $group = GroupContainer::where('id', '=', $group_id)->first();

                    return $group;
                }
            }

            return $full_equip;
        } else {
            return $equip;
        }
    }

    public function validateEquipment(array $equipment)
    {

        foreach ($equipment as $index => $eq) {
            if ($eq == "20") {
                $equipment[$index] = "1";
            } else if ($eq == "40") {
                $equipment[$index] = "2";
            } else if ($eq == "40HC") {
                $equipment[$index] = "3";
            } else if ($eq == "45") {
                $equipment[$index] = "4";
            }
            if ($eq == "40NOR") {
                $equipment[$index] = "5";
            } else if ($eq == "20RF") {
                $equipment[$index] = "6";
            } else if ($eq == "40RF") {
                $equipment[$index] = "7";
            } else if ($eq == "40HCRF") {
                $equipment[$index] = "8";
            } else if ($eq == "20OT") {
                $equipment[$index] = "9";
            } else if ($eq == "40OT") {
                $equipment[$index] = "10";
            } else if ($eq == "20FR") {
                $equipment[$index] = "11";
            } else if ($eq == "40FR") {
                $equipment[$index] = "12";
            }
        }
        return $equipment;
    }
}