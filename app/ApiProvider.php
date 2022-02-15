<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    protected $table = 'api_providers';

    public function search_carriers()
    {
        return $this->morphToMany(SearchCarrier::class,'provider','provider_type','provider_id');
    }
}
