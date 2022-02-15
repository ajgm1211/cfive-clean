<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StatusQuote extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //
}
