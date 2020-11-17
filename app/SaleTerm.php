<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SaleTerm extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
   	protected $fillable =['name','description','company_user_id'];
}
