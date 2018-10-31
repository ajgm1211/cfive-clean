<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileTmp extends Model
{
    protected $table = 'files_tmp';
    protected $fillable = ['contract_id','name_file'];
    
    public function Contract(){
        
        return $this->belognsTo('App\Contract');
        
    }
}
