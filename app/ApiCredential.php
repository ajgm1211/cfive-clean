<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    protected $fillable = [
        "model_type", "model_id", "status", "credentials", "api_provider_id"
    ];
}
