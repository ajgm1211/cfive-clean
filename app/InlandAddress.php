<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandAddress extends Model
{
    
    protected $fillable = ['id','quote_id','port_id','inland_address_id','address', 'type'];

    public function inland_totals()
    {
        return $this->hasMany('App\AutomaticInlandTotal','inland_address_id');
    }
    
    public function quote()
    {
        return $this->belongsTo('App\QuoteV2');
    }

    public function duplicate($quote)
    {
        $newInlandAddress = $this->replicate();
        $newInlandAddress->quote_id = $quote->id;
        $newInlandAddress->save(); 

        $this->load(
            'inland_totals'
        );
        
        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {
                $newRelationship = $relationRecord->duplicate($quote,$newInlandAddress);
            }
        }    

        return $newInlandAddress;
    }
}
