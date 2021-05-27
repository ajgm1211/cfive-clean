<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InlandProvider extends Model
{
    protected $fillable = ['provider_type', 'provider_id', 'automatic_inland_id'];

    /**
     * Get all of the owning imageable models.
     */
    public function provider_id()
    {
        return $this->morphTo();
    }
}
