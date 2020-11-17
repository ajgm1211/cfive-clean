<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandKm extends Model
{
    protected $fillable = [ 'id','inland_id','currency_id', 'json_containers' ];
  	
  	public function inland()
  	{
   		return $this->belongsTo('App\Inland');
 	}

  	public function currency()
  	{
    	return $this->belongsTo('App\Currency','currency_id');
 	}

 	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'json_containers' => 'array',
    ];

    protected $attributes = [
        'json_containers' => '{}',
        'currency_id' => 149,
    ];


    public function per_container(){
        $first = true;

        foreach ($this->json_containers as $key => $value) {
            
            if($first)
            {
                $first_value = $value;
                $first = false;

            } else 
                if($value != $first_value) return '-';
        }
        
        return $value ?? 0;
    }
}
