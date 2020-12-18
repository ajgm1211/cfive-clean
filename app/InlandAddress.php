<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandAddress extends Model
{
    
    protected $fillable = ['id','quote_id','port_id','inland_address_id','address'];

    public function inland_totals()
    {
        return $this->hasOne('App\AutomaticInlandTotal','inland_address_id');
    }
    
    public function duplicate($quote)
    {
        $new_inland_address = $this->replicate();
        $new_inland_address->quote_id = $quote->id;
        $new_inland_address->save(); 

        if($quote->type == 'FCL'){
            $this->load(
                'inland_totals'
            );
        }else if($quote->type == 'LCL'){
            $this->load(
                'inland_totals'
            );
        }

        $relations = $this->getRelations();

        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {

                $newRelationship = $relationRecord->replicate();
                $newRelationship->inland_address_id = $new_inland_address->id;
                $newRelationship->quote_id = $quote->id;
                $newRelationship->save();
            }
        }    

        return $new_inland_address;
    }

}
