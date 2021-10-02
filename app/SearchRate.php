<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchRate extends Model
{
    protected $casts = ['equipment' => 'array'];

    protected $fillable = [
        'id', 'pick_up_date', 'user_id', 'equipment', 'delivery', 'direction', 'type', 'company_user_id', 'user_id', 
        'company_id', 'contact_id', 'price_level_id', 'origin_charges', 'destination_charges', 'origin_address', 'destination_address',
        'show_rate_currency'
    ];

    public function search_ports()
    {
        return $this->hasMany('App\SearchPort');
    }

    public function origin_ports()
    {
        return $this->hasManyThrough('App\Harbor', 'App\SearchPort', 'search_rate_id', 'id', 'id', 'port_orig');
    }

    public function destination_ports()
    {
        return $this->hasManyThrough('App\Harbor', 'App\SearchPort', 'search_rate_id', 'id', 'id', 'port_dest');
    }
    public function origin_locations()
    {
        return $this->hasManyThrough('App\Location', 'App\SearchPort', 'search_rate_id', 'id', 'id', 'location_orig');
    }

    public function destination_locations()
    {
        return $this->hasManyThrough('App\location', 'App\SearchPort', 'search_rate_id', 'id', 'id', 'location_dest');
    }

    public function carriers()
    {
        return $this->hasManyThrough('App\Carrier', 'App\SearchCarrier', 'search_rate_id', 'id', 'id', 'provider_id')
        ->where('provider_type', 'App\Carrier');
    }

    public function api_providers()
    {
        return $this->hasManyThrough('App\ApiProvider', 'App\SearchCarrier', 'search_rate_id', 'id', 'id', 'provider_id')
        ->where('provider_type', 'App\ApiProvider');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function company()
    {
        return $this->belongsTo('App\CompanyUser', 'company_user_id');
    }

    public function incoterm()
    {
        return $this->belongsTo('App\Incoterm', 'incoterm_id');
    }

    public function client_company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function price_level()
    {
        return $this->belongsTo('App\Price');
    }

    public function direction()
    {
        return $this->belongsTo('App\Direction', 'direction', 'id');
    }

    public function delivery_type()
    {
        return $this->belongsTo('App\DeliveryType', 'delivery', 'id');
    }

    public function containers($type = 'model')
    {
        $containers = [];

        if(is_array($this->equipment)){
            $equip = $this->equipment;    
        }else{
            $equip = explode(",", str_replace(["\"", "[", "]"], "", $this->equipment));
        }
        
        foreach($equip as $code){
            if($type == 'model'){
                $newContainer = Container::where('code',$code)->first();
            }elseif($type == 'array'){
                $newContainer = Container::where('code',$code)->first()->toArray();
            }

            array_push($containers,$newContainer);
        }
        
        return $containers;
    }

}
