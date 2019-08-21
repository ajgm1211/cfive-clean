<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportationJob extends Model
{
    protected $ftable   = 'importation_jobs';
    protected $fillable = ['id', 'queue', 'payload','attempts','reserved_at','available_at','created_at']; 
}
